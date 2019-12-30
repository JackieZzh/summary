<?php
namespace app\index\controller;
use wx\weixinUser;
use think\Controller;
use app\index\model\Voter;
use app\index\model\Active;
use app\index\model\Action;
use app\index\model\RedisModel;
use app\index\model\Participant;

class Index extends Controller
{
    private $wx;
    private $times = 0;
    private $redis;

    public function __construct()
    {
        $this->wx = new weixinUser();
        $this->redis = new RedisModel();
    }

    /**
     * 投票之前
     * @param $id
     */
    public function beforeTheVote($id)
    {
        // 获取code
        $this->wx->getWxCode($id);
    }

    public function index($id)
    {
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        if(empty($code) && $this->times < 3){  // 重新获取code
            $this->beforeTheVote($id);
        } elseif (empty($code) && $this->times > 2){
            $webData["code"] = 100001;
            $webData["errmsg"] = "无法获取code 请稍后重试";
            $this->times = 0;
        } else {
            // 获取用户信息 并留存
            // 通过code获取info
            $info = $this->wx->getWxAccessToken2($code);
            if (!empty($info['errcode'])){ // 重头开始 刷新code
                $this->beforeTheVote($id);
            }
            $accessToken = $info['access_token'];
            $openid = $info['openid'];
            // 用户ip
            $action['accessIp'] = $this->getUserIp();
            $action['accessTime'] = time();

            // 通过$info中openId 和 accessToken 获取用户信息 授权获取
            $userInfo = $this->wx->getUserInfo2($accessToken, $openid);
            if ($userInfo === 4001) {  // 刷新accessToken
                $info['access_token'] = $this->wx->getRefreshToken($info['refresh_token']);
                $userInfo = $this->wx->getUserInfo2($info['access_token'], $info['openid']);
            }
            // 过滤emoji表情
            foreach ($userInfo as  $key => $value){
                $userInfo["nickname"] = $this->filterEmoji($userInfo["nickname"]);
            }
            $userInfo['accessIp'] = $action['accessIp'];
            $userInfo['accessTime'] = $action['accessTime'];

            $voter['openid'] = $openid;
            // 有则update  没有则插入数据
            $resId = Voter::checkInfo(array('openid'=>$openid));
            if (!empty($resId)) {
                // update 用户信息
                $saveData['accessTime'] = $action['accessTime'];
                $saveData['nickname'] = $userInfo['nickname'];
                $saveData['headimgurl'] = $userInfo['headimgurl'];
                $saveData['sex'] = $userInfo['sex'];
                Voter::updateIntoUserInfo($saveData, array("id" => $resId));
                $voter['id'] = $resId;
            } else {
                // insert 用户信息
                $voter['id'] = Voter::insertIntoUserInfo($userInfo);
            }
            // 测试页面查看 以贵州医院第一次活动为例
            if ($id == "test"){
                $activeInfo = Active::getActiveInfo('id, title, description, background_img, timer_img, background_color, goods_url, rote_url, begin, end, voter_can, timer_img, background_color_rote, is_show, is_del',3);
                $activeInfo['code'] = 200;
                $activeInfo['errorMsg'] = "活动进行中";
                $activeInfo['title'] = "示例:(贵州医院第一次活动)";
                $activeInfo['time'] = 999999;
                // 获取用户信息
                $data = Participant::getParticipant(3);
                $activeInfo['part'] = $data;
            }  else{
                // 获取活动信息
                $activeInfo = Active::getActiveInfo('id, title, description, background_img, timer_img, background_color, goods_url, rote_url, begin, end, voter_can, timer_img, background_color_rote, is_show, is_del',$id);
                $activeInfo = $this->checkActive($activeInfo);

                if ($activeInfo['code'] == 200){
                    // 获取用户信息
                    $data = Participant::getParticipant($id);
                    $activeInfo['part'] = $data;
                }
            }

            $activeInfo['voter'] = $voter;
        }
        
        return view("index/index", [
            'data' => $activeInfo,
        ]);
    }

    public function doVote()
    {
        // 验证游戏
        $activeInfo = Active::getActiveInfo('id, title, description, background_img, timer_img, background_color, goods_url, rote_url, begin, end, voter_can, timer_img, background_color_rote, is_show, is_del',$_GET['aid']);
        $resActive = $this->checkActive($activeInfo);
        if($resActive['code'] == 200){
            // 验证参与者
            $whereP['id'] = $_GET['pid'];
            $whereP['active_id'] = $_GET['aid'];
            $whereP['is_show'] = 1;
            $whereP['is_del'] = 1;
            $resPart = Participant::checkParticipant($whereP);
            if (!empty($resPart)){
                // 验证投票者
                $resV = Voter::checkInfo(array('openid'=>$_GET['openid'], 'id'=>$_GET['vid']));
                if(!empty($resV)){
                    // 投票预处理
                    $can = $activeInfo['voter_can'];
                    $resA = $this->redis->checkAndSet($_GET['aid'], $_GET['openid'], $can);
                    if(!$resA){
                        $webData['code'] = 2003;
                        $webData['errorMsg'] = "次数已用完";
                    } else{
                        // 投票
                        $dataV['aid'] = $_GET['aid'];
                        $dataV['vid'] = $_GET['vid'];
                        $dataV['pid'] = $_GET['pid'];
                        $dataV['time'] = time();
                        $resV = Action::doVote($dataV);
                        if (!empty($resV)){
                            // 更改票数
                            $map['id'] = $_GET['pid'];
                            $resP = Participant::updateParticipant($map);
                            if(!empty($resP)){
                                $webData['code'] = 200;
                                $webData['errorMsg'] = "投票成功";
                            } else {
                                $webData['code'] = 201;
                                $webData['errorMsg'] = "投票失败";
                            }
                        } else {
                            $webData['code'] = 201;
                            $webData['errorMsg'] = "投票失败";
                        }
                    }
                } else {
                    $webData['code'] = 2004;
                    $webData['errorMsg'] = "该参与者已被禁";
                }
            } else {
                $webData['code'] = 2002;
                $webData['errorMsg'] = "非法参数 错误码: 2002";
            }
        } else {
            $webData['code'] = 2001;
            $webData['errorMsg'] = $resActive['errorMsg'];
        }

        return json_encode($webData);
    }

    public function checkActive($activeInfo)
    {
        if(empty($activeInfo)){
            $activeInfo['code'] = 2003;
            $activeInfo['errorMsg'] = "抱歉, 活动未开放";
            $activeInfo['title'] = "抱歉, 活动未开放";
            $activeInfo['background_color'] = "#fff";
            $activeInfo['background_img'] = "";
            $activeInfo['id'] = 0;
        } else{
            if($activeInfo['begin'] > time()){
                $activeInfo['code'] = 2001;
                $activeInfo['errorMsg'] = "活动还未开始";
                $activeInfo['title'] = "活动还未开始";
                $activeInfo['time'] = $activeInfo['begin'] - time();
            } else if($activeInfo['end'] < time()){
                $activeInfo['code'] = 2002;
                $activeInfo['errorMsg'] = "本次活动已结束";
                $activeInfo['title'] = "本次活动已结束";
            }  else if($activeInfo['is_show'] != 1 || $activeInfo['is_del'] != 1){
                $activeInfo['code'] = 2004;
                $activeInfo['errorMsg'] = "抱歉, 活动未开放";
                $activeInfo['title'] = "抱歉, 活动未开放";
                $activeInfo['id'] = 0;
            } else {
                $activeInfo['code'] = 200;
                $activeInfo['errorMsg'] = "活动进行中";
                $activeInfo['errorMsg'] = "title";
                $activeInfo['time'] = $activeInfo['end'] - time();
            }
        }

        return $activeInfo;
    }

    /**
     * 排行榜
     */
    public function leaderBoard()
    {
        if(!empty($_GET['id'])){
            // 查询显示排名个数
            $res = Active::getActiveInfo('leader_board_num',$_GET['id']);
            if ($res['leader_board_num'] != 0){
                $limit = $res['leader_board_num'];
            } else {
                $limit = null;
            }
            // 查询排名
            $where['active_id'] = $_GET['id'];
            $where['is_show'] = 1;
            $where['is_del'] = 1;
            $data = Participant::getLeaderBoard("nick_name, real_name, avatar_url, votes", $where, $limit);
            if (!empty($data)){
                foreach ($data as $key => $value){
                    if(!empty($value['nick_name'])){
                        $data[$key]['name'] = $value['nick_name'];
                    } else {
                        $data[$key]['name'] = $value['real_name'];
                    }
                }
                $webData['code'] = 200;
                $webData['data'] = $data;
                $webData['errorMsg'] = "OK";
            } else {
                $webData['code'] = 2001;
                $webData['errorMsg'] = "暂无数据";
            }
        } else {
            $webData['code'] = 2002;
            $webData['errorMsg'] = "参数有误!";
        }
        return json_encode($webData);
    }

    /**
     * 过滤掉emoji表情
     * @param $str
     * @return string|string[]|null
     */
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
