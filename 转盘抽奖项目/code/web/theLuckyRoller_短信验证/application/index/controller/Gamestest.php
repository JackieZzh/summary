<?php

namespace app\index\controller;

use think\Controller;
use wx\weixinUser;
use app\index\model\Users;
use app\index\model\Action;
use app\index\model\GamesInfo;
use app\index\model\UserToGameStatus;
use think\cache\driver\Redis;

class Gamestest extends Controller
{
    private $wx;
    private $users;
    private $action;
    private $game;
    private $userStatus;
    private $redis;

    public function __construct()
    {
        $this->wx = new weixinUser();
        $this->users = new Users();
        $this->action = new Action();
        $this->game = new GamesInfo();
        $this->userStatus = new UserToGameStatus();
        $redis = new Redis;
        $this->redis = $redis->handler();
    }

    public function intoHtml()
    {
        return view("games/test");
    }

    /**
     * @param $gameId
     * 抽奖页之前的操作
     */
    public function beforeTheLuckyRoller($gameId)
    {
        if (empty($_GET['code'])){
            // 获取code
            $this->wx->getWxCode($gameId);
        }

        var_dump($_GET['code']);
    }

    /**
     * @param $gameId
     * @return string
     * 获取code后的回调函数
     */
    public function afterGetWxCode($gameId)
    {
        if(empty($_GET['code'])){  // 更换浏览器重新获取code
            $this->beforeTheLuckyRoller($gameId);
        }
        $code = $_GET['code'];
        $action['accessIp'] = $this->getUserIp();
        $action['accessTime'] = time();
        $webData = array(); // 返回的数据
        // 通过code获取info
        if ($code) {
            $info = $this->wx->getWxAccessToken2($code);

            $accessToken = $info['access_token'];
            $openid = $info['openid'];
            // 通过$info中openId 和 accessToken 获取用户信息 授权获取
            $userInfo = $this->wx->getUserInfo2($accessToken, $openid);
            if ($userInfo === 4001) {  // 刷新accessToken
                $info['access_token'] = $this->wx->getRefreshToken($info['refresh_token']);
                $userInfo = $this->wx->getUserInfo2($info['access_token'], $info['openid']);
            }
            // 有则update  没有则插入数据
            $resId = $this->users->getOneUserInfo(array("openId" => $openid), "id");
            if ($resId['id']) {
                // update 用户信息
                $action['uid'] = $resId['id'];
                $this->users->updateIntoUserInfo($userInfo, array("id" => $resId['id']));
            } else {
                // insert 用户信息
                $id = $this->users->insertIntoUserInfo($userInfo);
                $action['uid'] = $id;
            }
            //todo 获取活动数据  通过mysql表 (或者用redis) 判断是否能进行游戏
            // 获取活动数据 判断活动是否继续
            $gameInfo = $this->game->getGameInfo(array("id" => $gameId));
            if ($gameInfo['status'] == 0 || empty($gameInfo['status']) ) {
                $webData['code'] = 3001;
                $webData['errmsg'] = "该活动暂未开始或已结束";
            } else if(time() > $gameInfo["expirestime"]){
                    $webData['code'] = 3002;
                    $webData['errmsg'] = "该活动已结束";
            } else {
                // 判断该用户是否能参加此游戏
                $userToGameInfo = $this->userStatus->userTimes(array("uid" => $action['uid'], "gid" => $gameId));
                $uid = $action['uid'];
                $times = $gameInfo["usertimes"];
                $webData = $this->getWebData($userToGameInfo, $gameId, $uid, $times);
                $webData['headimgurl'] = $userInfo['headimgurl'];
                $webData['title'] = $gameInfo["title"];
                $webData['nickname'] = $userInfo['nickname'];
                $webData['gameId'] = $gameId;
                $webData['userId'] = $uid;

            }
            // action记录
            $action['gid'] = $gameId;
            $this->action->addAction($action);
            //return json_encode($webData);

        } else {
            //todo 获取code有误时 处理方法
            $webData['code'] = 3000;
            $webData['errmsg'] = "getCode error";
            //return json_encode($webData);
        }

        return view("games/luckyRoller", [
            'data' => $webData
        ]);
    }

    /**
     * @return string
     * 获取结果
     */
    public function getTheLuckyRollerResult()
    {
        $uid = input("post.userId");
        $gid = input("post.gameId");

        $where['uid'] = $uid;
        $where['gid'] = $gid;

        // 判断是否填写电话
        $userTel = $this->users->getOneUserInfo(array("id" => $uid), "tel");
        if(!empty($userTel["tel"])){
            // 获取次数 扣除次数
            $res = $this->userStatus->userTimes($where);
            if( is_null($res["times"]) ){
                // 获取物品
                $webData = $this->getGameGoods();
                $webData['times'] = "infinite";

            }elseif ( ($res["times"]-1) >= 0 ){
                // 获取物品
                $webData = $this->getGameGoods();
                $data["times"] = $res["times"] - 1;
                // 更新玩家次数
                $res = $this->userStatus->updateGameTimes($where, $data);
                if($res>0){ // 更新成功
                    $webData['times'] = $data["times"];
                    $webData['code'] = 200;
                } else { // 更新失败
                    $webData['code'] = 3005;
                    $webData['errmsg'] = "网络链接有误,请重试";
                }
            } else {
                $webData['code'] = 3004;
                $webData['errmsg'] = "你的次数已用完";
            }
        } else {
            $webData["code"] = 3006;
        }

        //todo 判断游戏和用户是状态
        /*$userToGameInfo = $this->userStatus->userTimes($where);
        $times = $userToGameInfo["times"];
        $webData = $this->getWebData($userToGameInfo, $gid, $uid, $times);
        if( $webData['code'] == 200 ){
            // 可参加游戏 判断次数
        }*/
        return json_encode($webData);
    }

    public function userCommitInfo()
    {
        $data["name"] = input("post.name");
        $data["tel"] = (int)input("post.tel");
        $data["realcity"] = input("post.realcity");
        $data["isshortsightedness"] = (int)input("post.shot");

        if (empty($data["name"])){
            $res["code"] = 4001;
            $res["errormsg"] = 4001;
        }

        $where["id"] = input("post.id");

        $res = $this->users->updateIntoUserInfo($data, $where);


        return json_encode($res);
    }


    public function getGameGoods()
    {
        //todo 获取奖品列表
        $goods = "";
        $num = rand(1,99999);
        switch ($num){
            case 1 : $goods = "奖品1"; break;
            case $num >1 && $num <100 : $goods = "奖品2"; break;
            case $num >=100 && $num <1000 : $goods = "奖品3"; break;
            case $num >=1000 && $num <10000 : $goods = "奖品4"; break;
            case $num >=10000 && $num <100000 : $goods = "谢谢参与"; break;
        }
        $webData["goods"] = $goods;
        return $webData;
    }

    private function getWebData($userToGameInfo,$gameId, $uid, $times){
        if ($userToGameInfo['status'] === 0 ) {
            $webData['code'] = 3003;
            $webData['errmsg'] = "请填写您的信息";
        } else {
            if ( is_null($userToGameInfo['status']) ){ // 初始化用户对该游戏的数据
                $userGameInfo["gid"] =  $gameId;
                $userGameInfo["uid"] =  $uid;
                $userGameInfo["status"] =  1;
                $userGameInfo["times"] =  $times;
                $this->userStatus->initUserGameInfo($userGameInfo);
                if (is_null($times)) {
                    $webData['times'] = "infinite";
                } else {
                    $webData['times'] = $times;
                }
            } else { // 获取用户对该游戏的数据
                if (is_null($userToGameInfo['times'])) {
                    $webData['times'] = "infinite";
                } else {
                    $webData['times'] = $userToGameInfo['times'];
                }
            }
            $webData['code'] = 200;
        }
        return $webData;
    }


    /**
     * @return string
     * 获取用户ip
     */
    private function getUserIp()
    {
        $ip = "";
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $res = preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
        return $res;
    }


}