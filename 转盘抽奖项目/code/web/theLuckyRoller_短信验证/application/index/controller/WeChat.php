<?php
namespace app\index\controller;
use think\Controller;
use wx\weixinUser;
use think\cache\driver\Redis;
use app\index\model\UserInfo;
use app\index\model\Goods;

class WeChat extends Controller
{
    private $wx;
    private $redis;
    private $userInfo;
    private $goods;


    public function __construct()
    {
        $this->wx = new weixinUser;
        $redis = new redis();
        $this->redis = $redis->handler();
        $this->userInfo = new UserInfo;
        $this->goods = new Goods();
    }
    public function onLogin()
    {
        $code = input("post.code");
        //$encryptedData = input("post.encryptedData"); //用户加密数据
        //$iv = input("post.iv"); //偏移向量
        $res = $this->wx->xcxLogin($code, 1);

        if(!empty($res["openId"])){
            $openId = $res["openId"];
            $webData["openId"] = $openId;
        } else {
            return "openId 获取失败,请重试!";
        }
        $userInfo["openid"] = $res["openId"];
        if(!empty($res["unionId"])){
            $userInfo["unionid"] = $res["unionId"];
            $webData["unionId"] = $res["unionId"];
        }

        return  json_encode($webData);
    }

    /**
     * @return string
     * 添加用户基本信息
     */
    public function appletAddOrUpdateUserInfo()
    {
        $info = array();
        $info["avatar"] = input("post.avatarUrl");
        $info["uid"] = input("post.openId");
        $info["nickname"] = input("post.nickName");
        $info["ip"] = $this->getUserIp();
        $info["createtime"] = time();

        // 判断用户是否存在
        $where["uid"] = $info["uid"];
        $res = $this->userInfo->findUser($where);

        if($res){ // 存在就更新
            $this->userInfo->updateUserInfo($info, $where);
            $webData["code"] = 2001;
        } else { // 不存在就插入
            $this->userInfo->insertIntoUserInfo($info);
            $webData["code"] = 2002;
        }
        return json_encode($webData);
    }


    /**
     * @return string
     * 用户提交信息 并获取奖品
     */
    public function submitNcUserInfo()
    {
        $data["childName"] = input("post.childName");
        $data["city"] = input("post.city");
        $data["province"] = input("post.province");
        $data["tel"] = input("post.tel");
        $data["question"] = input("post.whichQue");
        $openId = input("post.openId");

        if (empty($data["childName"]) || strlen($data["childName"]) > 50 ){
            $res["code"] = 4001;
            $res["errormsg"] = "请填写孩子姓名哦";
        } elseif(preg_match("/^1[34578]\d{9}$/", $data["tel"]) == 0){
            $res["code"] = 4002;
            $res["errormsg"] = "电话号码格式有误";
        }  elseif( empty($data["province"]) ){
            $res["code"] = 4003;
            $res["errormsg"] = "请选择省份";
        }  elseif( empty($data["city"]) ){
            $res["code"] = 4004;
            $res["errormsg"] = "请选择城市";
        } else {
            $where["uid"] = $openId;
            $this->userInfo->updateUserInfo($data, $where);
            $res["code"] = 200;
        }

        // 获取奖品
        $data = $this->getGameGoods(1, $openId);

        return json_encode($data);

    }


    /**
     * @param $gid
     * @param $uid
     * @return array
     * 获取中奖信息
     */
    private function getGameGoods($gid, $uid)
    {
        //获取奖品列表
        $goodsList = $this->goods->getGamesGoodsList(array("gid"=>$gid), "id, title, weight");

        // 获取结果操作
        $weight = 0; // 权重数
        $len = 0; // 基数位数

        foreach ($goodsList as $key => $value){
            $newLen = strlen($value["weight"]);
            if( $newLen>=$len ){ // 判断小数点后多少位
                $len = $newLen+1;
            }
            $weight += $value["weight"];
        }

        // 设置奖品中奖数值数组 goodsArr  起始值1 基数$baseNum
        $goodsArr = array();
        $begin = 1;
        $baseNum = (int)str_pad(1,$len,"0",STR_PAD_RIGHT);
        $resNum = mt_rand(1, $baseNum); // 判断结果数
        foreach ($goodsList as $key => $value){
            if($value["weight"] == 0){ // 权重为0时
                $goodsArr[$value["id"]][0] = 0;
                $goodsArr[$value["id"]][1] = 0;
                $goodsArr[$value["id"]]["goodsName"] = $value["title"];
            } else {
                $goodsArr[$value["id"]][0] = $begin;
                $goodsArr[$value["id"]][1] = $begin + ($value["weight"] - 1);
                $goodsArr[$value["id"]]["goodsName"] = $value["title"];
                $begin = $goodsArr[$value["id"]][1]+1;
            }
        }
        // 判断结果
        $res = array();
        foreach($goodsArr as $key => $value){
            if($resNum>=$value[0] &&  $resNum<= $value[1]){
                $res["goodsid"]= $key;
                $res["goodsName"]= $value["goodsName"];
                break;
            }
        }

        if (empty($res)){
            $webData["gamecode"] = 50001;  // 未中奖
        } else { // 判断中奖信息(redis库存) 填入数据库 若为空 则谢谢参与
            $redisJude = $this->redis->hGet("ncluckyGame", $res["goodsid"]);
            if(is_null($redisJude)){ // 游戏已结束
                $webData["gamecode"] = 50002;
            } else {
                // 判断库存是否足够
                $redisRes = $this->redis->hIncrBy("luckyGame".$gid, $res["goodsid"], -1);
                if( $redisRes>=0 ){ // 还有库存
                    $data["goodsid"] = $res["goodsid"];
                    $data["gid"] = $gid;
                    $data["uid"] = $uid;
                    $data["time"] = time();
                    $this->goods->insertIntoUserGoodsList($data);
                    $webData = $res;
                } else {
                    if ($gid == 1){
                        // 上海特殊处理 库存为空 则送干眼spa
                        $res["goodsid"]= 1;
                        $res["goodsName"]= "干眼Spa";
                        $data["goodsid"] = $res["goodsid"];
                        $data["gid"] = $gid;
                        $data["uid"] = $uid;
                        $data["time"] = time();
                    } else {
                        $webData["gamecode"] = 50001;  // 库存不足 就当未中奖
                    }

                }

            }
        }
        /*$goods = "";
        $num = rand(1,99999);
        switch ($num){
            case 1 : $goods = "奖品1"; break;
            case $num >1 && $num <100 : $goods = "奖品2"; break;
            case $num >=100 && $num <1000 : $goods = "奖品3"; break;
            case $num >=1000 && $num <10000 : $goods = "奖品4"; break;
            case $num >=10000 && $num <100000 : $goods = "谢谢参与"; break;
        }
        $webData["goods"] = $goods;*/
        return $webData;
    }

    public function appletJudgeLuckyGameStatus()
    {
        $openId = input("post.openId");
        // 判断游戏是否过期
        $res = $this->redis->get("luckyLotto");
        if(empty($res)){
            $webData["gameStatus"] = 1;
            return json_encode($webData);
        }
    }

    public function appletLuckyGetGoods()
    {
        $openId = input("post.openId");

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