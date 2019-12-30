<?php
namespace app\index\controller;

use wx\weixinUser2;
use think\Controller;
use app\index\model\CheckActive;
use app\index\model\CheckCheckIn;
use app\index\model\CheckParticipant;

class Check extends Controller
{
    private $wx;
    private $times = 0;

    public function __construct()
    {
        $this->wx = new weixinUser2();
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
            $webData["errorMsg"] = "无法获取code 请稍后重试";
            $this->times = 0;
        } else {
            // 获取用户信息 并留存
            // 通过code获取info
            $info = $this->wx->getWxAccessToken2($code);
            print_r($info);die;
            if (!empty($info['errcode'])){ // 重头开始 刷新code
                $this->beforeTheVote($id);
            }
            $accessToken = $info['access_token'];
            $openid = $info['openid'];
            $action['accessTime'] = time();

            // 通过$info中openId 和 accessToken 获取用户信息 授权获取
            $userInfo = $this->wx->getUserInfo2($accessToken, $openid);
            if ($userInfo === 4001) {  // 刷新accessToken
                $info['access_token'] = $this->wx->getRefreshToken($info['refresh_token']);
                $userInfo = $this->wx->getUserInfo2($info['access_token'], $info['openid']);
            }

            // 过滤emoji表情
            $voter['openid'] = $openid;
            // 有则update  没有则插入数据
            $checkInfo = CheckParticipant::checkInfo(array('open_id'=>$openid), 'id, real_name');
            $resId = $checkInfo['id'];

            $saveData['nick_name'] = $this->filterEmoji($userInfo["nickname"]);
            $saveData['avatar'] = $userInfo['headimgurl'];
            $saveData['gender'] = $userInfo['sex'];
            if (!empty($resId)) {
                // update 用户信息
                CheckParticipant::updateIntoUserInfo($saveData, array("id" => $resId));
                $voter['id'] = $resId;
                $checkInfo['real_name'] ? $voter['status'] = 1: $voter['status'] = 2;
            } else {
                // insert 用户信息
                $saveData['open_id'] = $openid;
                $saveData['create_time'] = $action['accessTime'];
                $voter['id'] = CheckParticipant::insertIntoUserInfo($saveData);
                $voter['status'] = 2;
            }

            if ($voter['id']){
                // 验证会议详情
                $webData = $this->checkActiveInfo($id);
                $webData['code'] == 200 ? $webData['voter'] = $voter : true;
                // 验证签到详情
                $result = $this->checkCheckIn($voter['id'], $id);
                if ($result){
                    $webData['check'] = 1;
                    $webData['check_time'] = date('m:d H:i', $result['check_time']);
                } else {
                    $webData['check'] = 2;
                }

            } else {
                $webData['code'] = 201;
                $webData['active']['title'] = '未知会议';
            }
        }

        return view("check/index", [
            'webData' => $webData
        ]);
    }

    /**
     * 执行签到
     * @return false|string
     */
    public function doCheck()
    {
        $param = $_POST;
        if (isset($param['name'])){
            // 更新用户表
            $data['real_name'] = $param['name'];
            $where['id'] = $param['id'];
            CheckParticipant::updateIntoUserInfo($data, $where);
        }


        //  验证活动信息
        $webData = $this->checkActiveInfo($param['a_id']);
        if ($webData['code'] == 200){
            // 验证签到信息
            $res = $this->checkCheckIn($param['id'], $param['a_id']);
            if ($res){
                $webData['code'] = 2001;
                $webData['errorMsg'] = "您已经签过了!";
            } else {
                // 执行签到
                $save['active_id'] = $param['a_id'];
                $save['user_id'] = $param['id'];
                $save['check_time'] = time();
                $result = CheckCheckIn::doCheckIn($save);
                if ($result){
                    $webData['code'] = 200;
                } else {
                    $webData['code'] = 2002;
                    $webData['errorMsg'] = "网络波动 签到失败!";
                }
            }
        }

        return json_encode($webData);
    }


    /**
     * 验证活动信息
     * @param $id
     * @return mixed
     */
    public function checkActiveInfo($id)
    {
        // 验证会议详情
        $where['is_show'] = 1;
        $where['is_del'] = 1;
        $where['id'] = $id;
        $activeInfo = CheckActive::getActiveInfo('id, title, begin_time, end_time, before_time', $where);
        if ($activeInfo){
            if($activeInfo['end_time'] < time() ){
                $webData['code'] = 2001;
                $webData['errorMsg'] = "会议已结束!";
            } else{
                // 验证是否开启提前签到
                if ($activeInfo['before_time']){
                    if ( ($activeInfo['begin_time'] - $activeInfo['before_time']*1*60) > time()){
                        $webData['code'] = 2003;
                        $webData['errorMsg'] = "会议暂未开始, 请稍后签到!";
                    } else {
                        $webData['code'] = 200;

                    }
                } else {
                    $webData['code'] = 200;
                }
            }
            $webData['active'] = $activeInfo;

        } else {
            $webData['code'] = 2002;
            $webData['errorMsg'] = "未知会议";
            $webData['active']['title'] = '未知会议';
        }

        return $webData;
    }

    /**
     * 验证签到
     * @param $id
     * @param $aid
     * @return mixed
     */
    public function checkCheckIn($id, $aid)
    {
        $where['active_id'] = $aid;
        $where['user_id'] = $id;

        return CheckCheckIn::checkCheckIn($where, 'id, check_time');
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
}