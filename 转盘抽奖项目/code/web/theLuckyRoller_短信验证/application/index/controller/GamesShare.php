<?php

namespace app\index\controller;

use app\index\model\Action;
use app\index\model\GamesInfo;
use app\index\model\Goods;
use app\index\model\Users;
use app\index\model\UserToGameStatus;
use think\cache\driver\Redis;
use think\Controller;
use wx\weixinUser;
use alisms\demo\sendSms;

class GamesShare extends Controller
{
    private $wx;
    private $sms;
    private $users;
    private $action;
    private $game;
    private $userStatus;
    private $goods;
    private $redis;
    private $times = 0; // 报错次数

    public function __construct()
    {
        $this->wx = new weixinUser();
        $this->sms = new sendSms();
        $this->users = new Users();
        $this->action = new Action();
        $this->game = new GamesInfo();
        $this->userStatus = new UserToGameStatus();
        $this->goods = new Goods();
        $redis = new Redis;
        $this->redis = $redis->handler();
    }


    /**
     * @param $gameId
     * 抽奖页之前的操作
     */
    public function beforeTheLuckyRoller($gameId)
    {
        // 获取code
        $this->wx->getWxCodeShare($gameId);
    }

    /**
     * @param $gameId
     * @return string
     * 获取code后的回调函数
     */
    public function afterGetWxCode($gameId)
    {
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        //$code = $_GET['code'];
        if(empty($code) && $this->times < 3){  // 更换浏览器重新获取code
            $this->beforeTheLuckyRoller($gameId);
        } elseif (empty($code) && $this->times >= 3){
            $webData["code"] = 100001;
            $webData["errorMsg"] = "无法获取code 请稍后重试";
            $this->times = 0;
        } else {
            // 获取游戏信息
            $gameInfo = $this->game->getGameInfo(array("id" => $gameId),"id, title, createtime, expirestime, status, usertimes, contact, whichunit");

            // 用户ip
            //$action['accessIp'] = $this->getUserIp();
            $action['accessTime'] = time();
            $webData = array(); // 返回的数据
            // 通过code获取info
            $info = $this->wx->getWxAccessToken2($code);
            if (!empty($info['errcode'])){ // 重头开始 刷新code
                $this->beforeTheLuckyRoller($gameId);
            }
            $accessToken = $info['access_token'];
            $openid = $info['openid'];
            // 通过$info中openId 和 accessToken 获取用户信息 授权获取
            $userInfo = $this->wx->getUserInfo2($accessToken, $openid);
            if ($userInfo === 4001) {  // 刷新accessToken
                $info['access_token'] = $this->wx->getRefreshToken($info['refresh_token']);
                $userInfo = $this->wx->getUserInfo2($info['access_token'], $info['openid']);
            }

            // 设置抽奖页面数据
            $tk = $this->redis->get('luckyGame|tk');
            if (!$tk){
                $ak = $this->redis->get("luckyGame|ak");
                if ($ak){
                    $tk = $this->wx->getTicket($ak)->ticket;
                    $this->redis->set('luckyGame|tk',$tk);
                    $this->redis->expire("luckyGame|tk", 7000);
                }else {
                    $accessK = $this->wx->getWxAccessToken3()->access_token;
                    if ($accessK){
                        $this->redis->set("luckyGame|ak", $accessK);
                        $this->redis->expire("luckyGame|ak", 7000);
                        $tk = $this->wx->getTicket($accessK)->ticket;
                        $this->redis->set('luckyGame|tk',$tk);
                        $this->redis->expire("luckyGame|tk", 7000);
                    } else {
                        $tk = '';
                    }
                }
            }



            // 过滤emoji表情
            foreach ($userInfo as  $key => $value){
                $userInfo["nickname"] = $this->filterEmoji($userInfo["nickname"]);
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
                $resId['id'] = $id;
            }
            // 验证活动
            $goodsList = $this->goods->getGamesGoodsList(array("gid"=>$gameId), "id, title, picurl");

            if ($gameInfo['status'] == 0 || empty($gameInfo['status']) ) {
                $webData['code'] = 3001;
                $webData['gameId'] = $gameId;
                $webData['errorMsg'] = "该活动暂未开始或已结束";
            } else if(time() > $gameInfo["expirestime"]){
                $webData['code'] = 3002;
                $webData['gameId'] = $gameId;
                $webData['errorMsg'] = "该活动已结束";
            } else {
                // 判断redis库存是否开启
                $redis_num = $this->redis->exists("luckyGame".$gameId);
                if ($redis_num){
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
                    $webData['goodsList'] = $goodsList;
                    $userJudge = $this->users->getOneUserInfo(array("id" => $uid), "tel");
                } else {
                    $webData['code'] = 3003;
                    $webData['gameId'] = $gameId;
                    $webData['errorMsg'] = "该活动未开启";
                }
            }
            $webData["contact"] = $gameInfo["contact"];
            $webData["whichunit"] = $gameInfo["whichunit"];
            $webData["gamecreatetime"] = date("Y-m-d ", $gameInfo["createtime"]);
            $webData["gameexpirestime"] = date("Y-m-d ", $gameInfo["expirestime"]);
            // 获取该用户中奖信息
            $getGoodsList = $this->goods->getSomeOneGiftList($resId["id"],$gameId);
            if ($getGoodsList){
                // 判断是否提交信息
                if (!empty($userJudge["tel"])){
                    foreach ($getGoodsList as $k => $v){
                        $getGoodsList[$k]["time"] = date("Y-m-d", $v["time"]);
                    }
                    $webData["myGoodsList"] = $getGoodsList;
                } else {
                    $webData["myGoodsList"] = null;
                }
            } else {
                $webData["myGoodsList"] = null;
            }
            // 获取最新中奖的10位用户中奖信息
            $newWining = $this->goods->getSomeGiftList($gameId);
            $webData["wining"] = $newWining;
            /*// action记录
            $action['gid'] = $gameId;
            $this->action->addAction($action);*/
        }
        $webData["randomNum"] =  rand(11,12);
        // 设置config
        $webData['wx_info']['timestamp'] = time();
        $webData['wx_info']['noncestr'] = $this->createNonceStr();
        $webData['wx_info']['appId'] = "wx481498bdb36ea4f0";
        $webData['wx_info']['signature'] = sha1("jsapi_ticket=".$tk."&noncestr=".$webData['wx_info']['noncestr']."&timestamp=".$webData['wx_info']['timestamp']."&url=".'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


        return view("games/index3", [
            'data' => $webData
        ]);
    }

    /**
     * 随机数
     * @param int $length
     * @return string
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    // 过滤掉emoji表情
    function filterEmoji($str)
    {
        $str = preg_replace_callback( '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }

    /**
     * 获取结果(旧)
     * @return string
     */
    public function getTheLuckyRollerResult_old()
    {
        $uid = input("post.userId");
        $gid = input("post.gameId");

        $where['uid'] = $uid;
        $where['gid'] = $gid;

        //todo 验证用户是否填写信息
        //print_r($this->sms->sendSms());

        // 判断redis库存是否开启
        $redis_num = $this->redis->exists("luckyGame".$gid);
        if($redis_num){
            // 判断是否填写电话
            $userTel = $this->users->getOneUserInfo(array("id" => $uid), "tel");
            if(strlen($userTel["tel"]) != 0){
                // 获取次数 扣除次数
                $res = $this->userStatus->userTimes($where);
                if( is_null($res["times"]) ){
                    // 获取物品
                    $webData = $this->getGameGoods($gid,$uid,1);
                    $webData['times'] = "infinite";
                }elseif ( ($res["times"]-1) >= 0 ){
                    // 获取物品
                    $webData = $this->getGameGoods($gid,$uid,1);
                    $data["times"] = $res["times"] - 1;
                    // 更新玩家次数
                    $res = $this->userStatus->updateGameTimes($where, $data);
                    if($res>0){ // 更新成功
                        $webData['times'] = $data["times"];
                        $webData['code'] = 200;
                    } else { // 更新失败
                        $webData['code'] = 3005;
                        $webData['errorMsg'] = "网络链接有误,请重试";
                    }
                } else {
                    $webData['code'] = 3004;
                    $webData['errorMsg'] = "你的次数已用完";
                }
                $webData['time'] = date("Y-m-d H:i:s", time());
            } else {
                // 未填写电话
                // 查看是否获取商品
                $goods = $this->goods->getSomeOneGiftList($uid,$gid);
                if(empty($goods)){
                    // 获取物品
                    $webData = $this->getGameGoods($gid,$uid,1);
                    $webData["code"] = 3006; // 填写信息后
                    $webData["errorMsg"] = "请填写信息后领取奖品";
                    // 更新玩家次数
                    $res = $this->userStatus->userTimes($where);
                    $data["times"] = $res["times"] - 1;
                    $this->userStatus->updateGameTimes($where, $data);
                } else {
                    // 已经获取过奖品
                    $webData = $goods[0];
                    $webData["goodsName"] = $goods[0]["title"];
                    $webData["code"] = 3006; // 填写信息后
                    $webData["errorMsg"] = "请填写信息后领取奖品";
                }
            }
        } else {
            $webData["code"] = 3007;
            $webData["errorMsg"] = "活动未开启!";
        }

        return json_encode($webData);
    }

    /**
     * 获取结果
     * @return false|string
     */
    public function getTheLuckyRollerResult()
    {
        $uid = input("post.userId");
        $gid = input("post.gameId");

        $where['uid'] = $uid;
        $where['gid'] = $gid;
        // 获取游戏信息
        $gameInfo = $this->game->getGameInfo(array("id" => $gid),"expirestime, status");
        if ($gameInfo['status'] != 1){
            $webData['code'] = 3001;
            $webData['errorMsg'] = "活动未开启!";
        } else if($gameInfo['expirestime'] < time()){
            $webData['code'] = 3002;
            $webData['errorMsg'] = "活动已结束!";
        } else {
            // 判断redis库存是否开启
            $redis_num = $this->redis->exists("luckyGame".$gid);
            if($redis_num){
                // 判断是否填写电话
                $userTel = $this->users->getOneUserInfo(array("id" => $uid), "tel");
                if(strlen($userTel["tel"]) != 0){
                    // 获取次数 扣除次数
                    $res = $this->userStatus->userTimes($where);
                    if( is_null($res["times"]) ){
                        // 获取物品
                        $webData = $this->getGameGoods($gid,$uid,1);
                        $webData['times'] = "infinite";
                    }elseif ( ($res["times"]-1) >= 0 ){
                        // 获取物品
                        $webData = $this->getGameGoods($gid,$uid,1);
                        $data["times"] = $res["times"] - 1;
                        // 更新玩家次数
                        $res = $this->userStatus->updateGameTimes($where, $data);
                        if($res>0){ // 更新成功
                            $webData['times'] = $data["times"];
                            $webData['code'] = 200;
                        } else { // 更新失败
                            $webData['code'] = 3005;
                            $webData['errorMsg'] = "网络链接有误,请重试";
                        }
                    } else {
                        $webData['code'] = 3004;
                        $webData['errorMsg'] = "您的次数已用完";
                    }
                    $webData['time'] = date("Y-m-d H:i:s", time());
                } else {
                    // 未填写电话
                    $webData["code"] = 3006;
                    $webData["errorMsg"] = "请填写信息后领取奖品";
                }
            } else {
                $webData["code"] = 3007;
                $webData["errorMsg"] = "活动未开启!";
            }
        }

        return json_encode($webData);
    }

    /**
     * 提交用户信息
     * @return string
     */
    public function userCommitInfo()
    {
        // 数据验证
        $data['realname'] = input('post.realname');
        $data['tel'] = input('post.tel');
        $data["choose"] = input('post.choose');
        if (empty($data["realname"]) || strlen($data["realname"]) > 50 ){
            $webData["code"] = 4001;
            $webData["errorMsg"] = "用户名未填写或超出字数限制!";
        } elseif(preg_match("/^1[3456789]\d{9}$/", $data["tel"]) == 0){
            $webData["code"] = 4002;
            $webData["errorMsg"] = "电话号码格式有误!";
        } elseif(empty($data["choose"])){
            $webData["code"] = 4006;
            $webData["errorMsg"] = "请选择预报活动!";
        } else {
            // 验证code
            $code = input('post.code');
            if(!$code){
                $wenData['code'] = 4003;
                $webData['errorMsg'] = "请填写验证码!";
            } else {
                $uid = input("post.id");
                if ($this->checkCode($data["tel"], $code)){
                    // 更新数据
                    $where["id"] = $uid;
                    $resUpdata = $this->users->updateIntoUserInfo($data, $where);
                    if ($resUpdata["code"] == 60001){
                        $webData["code"] = 4004; // 更新失败
                        $webData['errorMsg'] = "网络连接错误, 请重试!";
                    } else {
                        $webData["code"] = 200;
                    }
                } else{
                    $webData['code'] = 4005;
                    $webData['errorMsg'] = "验证码有误, 请重试!";
                }
            }
        }

        return json_encode($webData);
    }

    /**
     * 获取验证码
     * @return false|string
     */
    public function getCode()
    {
        $tel = input('post.tel');
        if(preg_match("/^1[3456789]\d{9}$/", $tel) == 0){
            $webData["code"] = 5001;
            $webData["errorMsg"] = "电话号码格式有误";
        } else {

            // 发送短信
            $code = $this->createCode();
            $smsRes = $this->sms->sendSms($tel, $code);
            if($smsRes->Code === "OK"){
                $webData['code'] = 200;
                $webData['code_num'] = $code;
                // 设置缓存
                $this->redis->Set("theLuckyRoller".$tel, $code);
                $this->redis->expire("theLuckyRoller".$tel, 900);
            } else {
                $webData['code'] = 2001;
                $webData['errorMsg'] = $smsRes->Message;
            }

        }

        return json_encode($webData);
    }

    /**
     * 生成验证码
     * @return int
     */
    private function createCode(){
        return rand(1000, 9999);
    }

    /**
     * 验证code
     * @param $tel
     * @param $code
     * @return bool
     */
    private function checkCode($tel, $code){
        $res = $this->redis->Get('theLuckyRoller'.$tel);
        if ($res == $code) {
            // 验证成功, 注销验证码
            $this->redis->Del('theLuckyRoller'.$tel);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取奖品
     * @param $gid
     * @param $uid
     * @param $type
     * @return array
     */
    private function getGameGoods($gid, $uid, $type)
    {
        //获取奖品列表
        $goodsList = $this->goods->getGamesGoodsList(array("gid" => $gid), "id, title, weight");
        // 获取结果操作
        $weight = 0; // 权重数
        $len = 0; // 基数位数

        foreach ($goodsList as $key => $value) {
            $newLen = strlen($value["weight"]);
            if ($newLen >= $len) { // 判断小数点后多少位
                $len = $newLen + 1;
            }
            $weight += $value["weight"];
        }

        // 设置奖品中奖数值数组 goodsArr  起始值1 基数$baseNum
        $goodsArr = array();
        $begin = 1;
        $baseNum = (int)str_pad(1, $len, "0", STR_PAD_RIGHT);
        $resNum = mt_rand(1, $baseNum); // 判断结果数
        foreach ($goodsList as $key => $value) {
            if ($value["weight"] == 0) { // 权重为0时
                $goodsArr[$value["id"]][0] = 0;
                $goodsArr[$value["id"]][1] = 0;
                $goodsArr[$value["id"]]["goodsName"] = $value["title"];
            } else {
                $goodsArr[$value["id"]][0] = $begin;
                $goodsArr[$value["id"]][1] = $begin + ($value["weight"] - 1);
                $goodsArr[$value["id"]]["goodsName"] = $value["title"];
                $begin = $goodsArr[$value["id"]][1] + 1;
            }
        }
        // 判断结果
        $res = array();
        foreach ($goodsArr as $key => $value) {
            if ($resNum >= $value[0] && $resNum <= $value[1]) {
                $res["goodsid"] = $key;
                $res["goodsName"] = $value["goodsName"];
                break;
            }
        }

        //var_dump($res);die;

        if (empty($res)) {
            $webData["gamecode"] = 50001;  // 未中奖
        } else { // 判断中奖信息(redis库存) 填入数据库 若为空 则谢谢参与或特殊处理
            $redisJude = $this->redis->hGet("luckyGame" . $gid, $res["goodsid"]);
            if (is_null($redisJude)) { // 游戏已结束
                $webData["gamecode"] = 50002;
            } else {
                // 判断库存是否足够
                $redisRes = $this->redis->hIncrBy("luckyGame" . $gid, $res["goodsid"], -1);
                if ($redisRes >= 0) { // 还有库存
                    $data["goodsid"] = $res["goodsid"];
                    $data["gid"] = $gid;
                    $data["uid"] = $uid;
                    $data["time"] = time();
                    if ($type === 1) { // 用户已填手机号 则更新至list表
                        $this->goods->insertIntoUserGoodsList($data);
                    }
                    $webData = $res;
                } else {
                    if ($gid == 1) {
                        // 上海特殊处理 库存为空 则送干眼spa
                        $res["goodsid"] = 1;
                        $res["goodsName"] = "干眼Spa";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 2) {
                        // 昆明
                        $res["goodsid"] = 14;
                        $res["goodsName"] = "免费14项近视健康检查";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 3) {
                        // 昆明
                        $res["goodsid"] = 20;
                        $res["goodsName"] = "术前检查券";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 4) {
                        // 昆明
                        $res["goodsid"] = 30;
                        $res["goodsName"] = "30元电脑综合验光一次";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 5) {
                        // 昆明
                        $res["goodsid"] = 35;
                        $res["goodsName"] = "激光近视术前检查";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 6) {
                        // 昆明
                        $res["goodsid"] = 55;
                        $res["goodsName"] = "视力检查免费券";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 7) {
                        // 昆明
                        $res["goodsid"] = 64;
                        $res["goodsName"] = "近视眼健康检查";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 8) {
                        // 昆明
                        $res["goodsid"] = 69;
                        $res["goodsName"] = "免费眼部健康体检";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    } elseif ($gid == 9) {
                        // 昆明
                        $res["goodsid"] = 77;
                        $res["goodsName"] = "眼健康检查";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    }  elseif ($gid == 10) {

                        $res["goodsid"] = 69;
                        $res["goodsName"] = "200元近视手术术前检查免费做";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    }   elseif ($gid == 14) {

                        $res["goodsid"] = 113;
                        $res["goodsName"] = "近视配镜7折券";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                        if ($type === 1) { // 用户已填手机号 则更新至list表
                            $this->goods->insertIntoUserGoodsList($data);
                        }
                        $webData = $res;
                    }
                }
            }
        }
        return $webData;
    }

    /**
     * 页面关闭/外用回退方法
     */
    public function errorCommitUserInfo()
    {
        $gameId = input("post.gameId");
        $goodsId = input("post.goodsId");
        $uid = input("post.uId");
        $status = input("post.status");
        if($status == 1){
            // 判断用户是否提交成功信息
            $userTel = $this->users->getOneUserInfo(array("id" => $uid), "tel");
            if (strlen($userTel["tel"]) == 0){ // 回退
                $res = $this->redis->hGet("luckyGame".$gameId, $goodsId);
                if($res <= 0){
                    $this->redis->hSet("luckyGame".$gameId, $goodsId, 1);
                } else {
                    $this->redis->hIncrBy("luckyGame".$gameId, $goodsId, 1);
                }
            }
        }
    }


    /**
     * 用户提交信息失败时  将获取的物品回退
     */
    private function errorCommit($goodsId, $gameId)
    {
        $res = $this->redis->hGet("luckyGame".$gameId, $goodsId);
        if($res <= 0){
            $this->redis->hSet("luckyGame".$gameId, $goodsId, 1);
        } else {
            $this->redis->hIncrBy("luckyGame".$gameId, $goodsId, 1);
        }
    }




    private function getWebData($userToGameInfo,$gameId, $uid, $times){
        if ($userToGameInfo['status'] === 0 ) {
            $webData['code'] = 3003;
            $webData['errorMsg'] = "请填写您的信息";
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