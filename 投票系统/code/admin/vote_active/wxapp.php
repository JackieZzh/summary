<?php
defined('IN_IA') or exit('Access Denied');
require_once('res/comment/api/curl.php');
class Applet_csModuleWxapp extends WeModuleWxapp
{
    private $appId = 'wx680541fd2f67da3f';
    private $secret = '06165525120cf8d61da3fbfd40493b0c';

    /**
     *  session_key表
     * @var string
     */
    private $query;
    private $redis;
    private $tb_wx_applet_test_loginKey = "wx_applet_test_loginKey";
    private $tb_wx_applet_test_userInfo = "wx_applet_test_userInfo";
    private $tb_wx_applet_test_hospital = "applet_test_hospital";
    private $tb_banner = "applet_test_active_list";
    private $tb_class = "applet_test_classify";
    private $tb_docRel = "applet_test_doctor_relation";
    private $tb_doc = "applet_test_doctor";
    private $tb_sch = "applet_test_scheduling";
    private $tb_login_active = "applet_test_user_login_active";
    private $tb_login_statistics = "applet_test_statistics";

    public function __construct()
    {
        global $_W;
        $this->query = load()->object('query');
        $redisConfig = $_W["config"]['setting']['redis'];
        $redis = new Redis();
        $this->redis = $redis->pconnect($redisConfig['server'], $redisConfig['port'],$redisConfig['timeout']);
    }

    /**
     * 用户登录
     * @return false|string
     */
    public function doPageUserLogin()
    {
        global $_W, $_GPC;
        /*$redis = new Redis();
        $redis->set('applet_test', '111');
        $res = $redis->get('applet_test');*/
        $code = $_GPC['code'];
        $res = curl::sendRequest("https://api.weixin.qq.com/sns/jscode2session?appid=".$this->appId."&secret=".$this->secret."&js_code=".$code."&grant_type=authorization_code", null, 1);
        if (!empty($res['session_key'])){
            $where['openId'] = $res['openid'];
            //$dataUser['openId'] = $res['openid'];
            // 判断是否需要插入用户新数据
            $userRes = pdo_get($this->tb_wx_applet_test_userInfo, $where);
            if(empty($userRes)){
                $webData['code'] = 201;
                $webData['openId'] = $res['openid'];
            }else {
                $webData['code'] = 200;
                $webData['uid'] = $userRes['id'];
                $webData['lv'] = $userRes['level'];
                $webData['top'] = $userRes['top'];
                $webData['openId'] = $res['openid'];
            }
            // 判断openId 有无对应key可用 后续需要session_key时可开启
            /*$key = pdo_get($this->tb_wx_applet_test_loginKey, $where);
            if(!empty($key['key'])){
                $upData['key'] = $res['session_key'];
                $resUp = pdo_update($this->tb_wx_applet_test_loginKey, $upData, $where);
                if (!empty($resUp)){
                    $webData['code'] = 200;
                    $webData['openId'] = $res['openid'];
                } else {
                    $webData['code'] = 202;
                    $webData['errMsg'] = "更新数据失败";
                }
            } else{
                // 新增key表
                $data['key'] = $res['session_key'];
                $data['openId'] = $res['openid'];
                $result = pdo_insert($this->tb_wx_applet_test_loginKey, $data, 'INSERT');
                if (!empty($result)) {
                    $webData['code'] = 200;
                    $webData['openId'] = $data['openId'];
                } else {
                    $webData['code'] = 201;
                    $webData['errMsg'] = "插入数据失败";
                }
            }*/
        } else {
            $webData['code'] = $res['errcode'];
            $webData['errMsg'] = $res['errmsg'];
        }
        return json_encode($webData);
    }

    /**
     * 新用户信息留存
     */
    public function doPageRegister()
    {
        global $_W, $_GPC;
        // https://weixin.prykweb.com/attachment/images/22/2019/06/H7SqsEqQ2rFY66eryPYaBUfQq9FQuX.jpg
        $dataUser['nickName'] = $_GPC['nickName'];
        $dataUser['country'] = $_GPC['country'];
        $dataUser['city'] = $_GPC['city'];
        $dataUser['avatarUrl'] = $_GPC['avatarUrl'];
        $dataUser['gender'] = $_GPC['gender'];
        $dataUser['openId'] = $_GPC['openId'];
        $dataUser['uniacid'] = 22;
        $dataUser['add_time'] = time();
        $dataUser['bd_country'] = $_GPC['bd_country'];
        $dataUser['bd_city'] = $_GPC['bd_city'];
        $dataUser['bd_district'] = $_GPC['bd_district'];
        $dataUser['bd_province'] = $_GPC['bd_province'];
        if($_GPC['referrer'] !== '' && $_GPC['referrer']!= null && $_GPC['referrer'] != 'null'){
            $dataUser['referrer'] = $_GPC['referrer'];
        }
        if ($_GPC['lv']){
            $dataUser['level'] = $_GPC['lv'];
        }
        if ($_GPC['top'] !== '' && $_GPC['top']!= null && $_GPC['top'] != 'null'){
            $dataUser['top'] = $_GPC['top'];
        }
        $dataUser['ip'] = $this->getUserIp();

        if (!empty($_GPC['avatarUrl'])){
            $dataUser['avatarUrl'] = $_GPC['avatarUrl'];
        } else {
            $dataUser['avatarUrl'] = "https://weixin.prykweb.com/attachment/images/22/2019/06/H7SqsEqQ2rFY66eryPYaBUfQq9FQuX.jpg";
        }
        $res = pdo_insert($this->tb_wx_applet_test_userInfo, $dataUser);
        if (!empty($res)){
            $webData['code'] = 200;
        } else {
            $webData['code'] = 201;
        }   

        return json_encode($webData);
    }

    /**
     * 用户登陆留存
     * @return false|string
     */
    public function doPageUserLoginAction()
    {
        global $_W, $_GPC;
        $data['uid'] = $_GPC['uid'];
        $data['time'] = time();
        $nowDay = strtotime(date("Y-m-d"));

        // 这里的redis好像无法使用
        /*if (!$this->redis->get('applet_test_login'.$data['uid'])){
            // 获取当天23:59:59 的时间戳
            $lastSeconds = strtotime(date("Y-m-d 23:59:59"));
            $exp = $lastSeconds - $data['time'];
            $this->redis->set('applet_test_login'.$data['uid'], 1, $exp);
            $sum = $this->redis->incr('applet_test_login_sum');
            // 更新总登陆次数 (此处要做并发处理)
            $where['time'] = $nowDay;
            $res = $this->query->from($this->tb_login_statistics)->where($where)->get();
            if (!empty($res)){
                $data['time'] = $nowDay;
                $data['new_login'] = $sum;
                // 插入数据库
                pdo_insert('wx_applet_test_statistics', $data);
            }
        }*/

        $data['country'] = $_GPC['country'];
        $data['province'] = $_GPC['province'];
        $data['city'] = $_GPC['city'];
        $data['district'] = $_GPC['district'];
        $data['model'] = $_GPC['model'];
        $data['internet'] = $_GPC['internet'];
        $data['scene'] = $_GPC['scene'];
        //$data['uniacid'] = $_W['uniacid'];
        $data['uniacid'] = 22;
        // 获取用户ip
        $data['ip'] = $this->getUserIp();
        $res = pdo_insert($this->tb_login_active, $data);
        if (empty($res)){
            $webData['code'] = 201;
        } else {
            $webData['code'] = 200;
        }

        return json_encode($webData);
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

    // 医院列表
    public function doPageGetHospitalList()
    {
        global $_W, $_GPC;
        $page = $_GPC['page'];
        $limit = $_GPC['limit'];

        $list = $this->query->from($this->tb_wx_applet_test_hospital)->select('h_name', 'h_logo', 'id', 'h_telephone', 'h_sort', 'h_address')->page($page, $limit)->orderby('h_sort', 'desc')->getall();
        if (!empty($list)){
            $webData['code'] = 200;
            $webData['data'] = $list;
        } else {
            $webData['code'] = 201;
        }
        return json_encode($webData);
    }
    
    // 医院活动列表
    public function doPageGetHospitalActive()
    {
        global $_W, $_GPC;
        $where['hid'] = $_GPC['hid'];
        $where['is_hide'] = 0;
        $where['is_del'] = 0;

        $banner = $this->query->from($this->tb_banner)->select('id', 'title', 'image', 'sort')->where($where)->page(0,3)->orderby(['sort'=>'desc','id'=>'desc'])->getall();
        if (!empty($banner)){
            $webData['code'] = 200;
            $webData['banner'] = $banner;
        } else {
            $webData['code'] = 201;
        }

        return  json_encode($webData);
    }

    // 医院科室列表
    public function doPageGetDepartmentList()
    {
        global $_W, $_GPC;
        $where['c_hospitals'] = $_GPC['hid'];
        $where['c_hidden'] = 0;
        $where['c_isdel'] = 0;
        $list = $this->query->from($this->tb_class)->select('id', 'c_name', 'c_name_en')->where($where)->getall();
        if (!empty($list)){
            $webData['code'] = 200;
            $webData['list'] = $list;
        } else {
            $webData['code'] = 201;
        }

        return  json_encode($webData);
    }

    // 获取医生列表
    public function doPageGetDoctorList()
    {
        global $_W, $_GPC;

        $where['dr.c_id'] = $_GPC['cid'];
        $where['do.is_del'] = 0;
        $where['do.is_show'] = 0;

        $list = $this->query->from($this->tb_docRel, 'dr')->leftjoin($this->tb_doc, 'do')->on('dr.d_id', 'do.id')->select('do.id', 'do.doc_name',  'do.doc_sort', 'do.doc_img', 'do.doc_good')->where($where)->orderby(['do.doc_sort'=>'desc', 'do.id'=>'asc'])->getall();

        if (!empty($list)){
            $webData['code'] = 200;
            $webData['list'] = $list;
        } else {
            $webData['code'] = 201;
        }

        return  json_encode($webData);
    }

    public function doPageGetDoctorScheduling()
    {
        global $_W, $_GPC;

        $where['doc_id'] = $_GPC['doc_id'];
        $where['class_id'] = $_GPC['cid'];
        $date = strtotime($_GPC['date']);
        $where['date >='] = $date;

        $list = $this->query->from($this->tb_sch)->select('id', 'doc_id', 'class_id', 'date', 'reserve', 'reserved')->where($where)->orderby('date', 'asc')->getall();
        foreach($list as $key => $value){
            $list[$key]['date'] = date('Y/m/d', $list[$key]['date']);
            if ( ((int)$list[$key]['reserve'] - (int)$list[$key]['reserved']) > 0 ){
                $list[$key]['code'] = 1;
            } else {
                $list[$key]['code'] = 0;
            }
        }

        if (empty($list)){
            $webData['code'] = 201;
        } else {
            $webData['code'] = 200;
            $webData['list'] = $list;
        }
        return json_encode($webData);
    }
}

