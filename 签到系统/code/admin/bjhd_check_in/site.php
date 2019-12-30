<?php
/**
 * 公众号点赞应用
 */
defined('IN_IA') or exit('Access Denied');

class bjhd_check_inModuleSite extends WeModuleSite
{
    private $query;
    private $hospital = 'vote_active_hospital';
    private $hos_user = 'vote_hospital_user';
    private $user = 'users';
    private $action = "vote_active_action";
    private $voter = "vote_active_voter";

    private $active = 'bjhd_active';
    private $part = 'bjhd_active_participant';
    private $check = 'bjhd_active_check_in';
    //private $redis;
    public function __construct()
    {
        global $_W;
        $this->query = load()->object('query');
        /*$redisConfig = $_W["config"]['setting']['redis'];
        $redis = new Redis();
        $redis->pconnect($redisConfig['server'], $redisConfig['port'],$redisConfig['timeout']);
        $this->redis = $redis;*/
    }

    // 活动管理
    public function doWebActive()
    {
        global $_W, $_GPC;
        session_start();

        $uid = $_W['uid'];
        if($uid != 1){
            // 先查询 改用户拥有的医院权限
            $map['uid'] = $uid;
            $hids = $this->query->from($this->hos_user)->select('hid')->where($map)->get();
            $his = explode(',', $hids['hid']);
            $where['id in'] = $his;
        }
        // 获取医院列表
        $where['is_del'] = 1;
        $where['is_show'] = 1;
        $hospitalList = $this->query->from($this->hospital)->select('id', 'title')->where($where)->getall();
        if(isset($_SESSION['vote_choose_hid'])){
            $hid = $_SESSION['vote_choose_hid'][$_W['uid']];
        } else {
            $hid = 0;
        }
        $length = count($hospitalList);
        include $this->template('active');
    }

    // 获取活动列表
    public function doWebGetActiveInfo()
    {
        global $_W, $_GPC;

        if (empty($_GPC['hid'])){
            $webData['data'] = [];
            $webData['count'] = 0;
            $webData['code'] = 0;
        } else {
            session_start();
            // 是否查询字段
            if(!empty($_GPC['search'])){
                $where['title like'] = "%".$_GPC['search']."%";
            }
            // 是否有按字段排序
            if(!empty($_GPC['field'])){
                $field = $_GPC['field'];
                $order = $_GPC['order'];
            } else {
                $field = 'sort';
                $order = 'desc';
            }
            // 分页
            $page = $_GPC['page'];
            $limit = $_GPC['limit'];

            // 公用条件
            $where['is_del'] = 1;
            $where['hospital_id'] = $_GPC['hid'];
            $activeList = $this->query->from($this->active)->select('id', 'title', 'create_time',  'sort', 'begin_time', 'end_time', 'is_show')->where($where)->orderby($field, $order)->page($page, $limit)->getall();
            foreach($activeList as $key => $value){
                $activeList[$key]['create_time'] = date('m-d', $value['create_time']);
                if(time() >= $value['end_time']){
                    $activeList[$key]['status'] = 1;
                } else if ( time() >= $value['begin_time'] && time() < $value['end_time'] ){
                    $activeList[$key]['status'] = 2;
                } else if ( time() < $value['begin_time'] ){
                    $activeList[$key]['status'] = 3;
                }
            }
            // 设置session
            $_SESSION['bjhd_choose_hid'] = array( $_W['uid'] => $_GPC['hid']);
            $webData['data'] = $activeList;
            $webData['count'] = $this->query->from($this->active)->select('id')->where($where)->count();
            $webData['code'] = 0;
        }

        return json_encode($webData);
    }

    // 活动详情页面
    public function doWebEditActive()
    {
        global $_W, $_GPC;
        if($_GPC['type'] == 1){
            // 添加活动
            $type = 1;
        } elseif($_GPC['type'] == 2){
            // 编辑或查看活动
            $type = 2;
            $data = $this->query->from($this->active)->select('id', 'title', 'begin_time', 'end_time',  'is_show', 'sort', "before_time")->where(array("id" => $_GPC['id']))->get();
            if(!empty($data)){
                $data['begin'] = date('Y-m-d H:i:s', $data['begin_time']);
                $data['end'] = date('Y-m-d H:i:s', $data['end_time']);
            }
        }

        include $this->template('editActive');
    }

    // 添加或修改活动
    public function doWebAddOrUpdateActive ()
    {
        global $_W, $_GPC;
        if (empty($_GPC['active_time'])){
            $webData['code'] = 2002;
            $webData['errorMsg'] = "请选择开始时间!";
        } else if (empty($_GPC['title'])){
            $webData['code'] = 2003;
            $webData['errorMsg'] = "活动名不能为空";
        } else {
            if($_GPC['is_show'] == 'on'){
                $data['is_show'] = 1;
            } else {
                $data['is_show'] = 2;
            }

            // 解析时间
            $time = explode('~', $_GPC['active_time']);
            if(count($time) === 2){
                $data['begin_time'] = strtotime($time[0]);
                $data['end_time'] = strtotime($time[1]);
                $data['title'] = $_GPC['title'];
                $data['sort'] = $_GPC['sort'];
                $data['before_time'] = $_GPC['before_time'];

                if(empty($_GPC['type'])){
                    $webData['code'] = 2001;
                    $webData['errorMsg'] = "上传方式有误";
                } else if ($_GPC['type'] == 1){
                    $data['hospital_id'] = $_GPC['hid'];
                    $data['create_time'] = time();
                    $data['creator'] = $_W['username'];
                    // 插入 数据库
                    $res = pdo_insert('wx_'.$this->active, $data);
                    if (empty($res)){
                        $webData['code'] = 201;
                        $webData['errorMsg'] = "添加失败, 请重试或联系管理员...";
                    } else{
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "添加成功!";
                    }
                } else if ($_GPC['type'] == 2){
                    // 编辑
                    $where['id'] = $_GPC['id'];
                    $res = pdo_update('wx_'.$this->active, $data, $where);
                    if (empty($res)){
                        $webData['code'] = 201;
                        $webData['errorMsg'] = "修改失败或没有任何修改";
                    } else{
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "修改成功!";
                    }
                } else {
                    $webData['code'] = 2004;
                    $webData['errorMsg'] = "参数有误 错误码: 2004";
                }

            } else {
                $webData['code'] = 2004;
                $webData['errorMsg'] = "活动时间选择有误";
            }

        }

        return json_encode($webData);
    }

    // 参与者详情页
    public function doWebParticipantList()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];

        include $this->template('participant');
    }

    // 参与者详情
    public function doWebGetCheckInInfo()
    {
        global $_W, $_GPC;

        // 是否有按字段排序
        if(!empty($_GPC['field'])){
            $field = $_GPC['field'];
            $order = $_GPC['order'];
        } else {
            $field = 'id';
            $order = 'desc';
        }
        // 分页
        $page = $_GPC['page'];
        $limit = $_GPC['limit'];

        // 是否查询字段
        if(!empty($_GPC['search'])){
            $where['p.real_name like'] = "%".$_GPC['search']."%";
            $data = $this->query->from($this->check, 'c')->leftjoin($this->part, 'p')->on('c.user_id', 'p.id')->leftjoin($this->active, 'a')->on('c.active_id','a.id')->select('c.id, c.check_time, p.real_name, p.nick_name, p.avatar, a.title')->where($where)->orderby($field, $order)->page($page, $limit)->getall();
        } else {
            $data = $this->query->from($this->check, 'c')->leftjoin($this->part, 'p')->on('c.user_id', 'p.id')->leftjoin($this->active, 'a')->on('c.active_id','a.id')->select('c.id, c.check_time, p.real_name, p.nick_name, p.avatar, a.title')->orderby($field, $order)->page($page, $limit)->getall();
        }

        if ($data){
            foreach ($data as $k => $v){
                $data[$k]['check_time'] = date('Y-m-d H:i:s', $v['check_time']);
            }
        }
        $webData['data'] = $data;
        $webData['count'] = $this->query->from($this->check)->select('id')->count();
        $webData['code'] = 0;

        return json_encode($webData);
    }










    public function doWebUser()
    {
        global $_W, $_GPC;
        include $this->template('users');
    }







    // 获取参与者信息
    public function doWebGetParticipantInfo()
    {
        global $_W, $_GPC;
        if (empty($_GPC['aid'])){
            $webData['data'] = [];
            $webData['count'] = 0;
            $webData['code'] = 0;
        } else {
            // 是否查询字段
            if(!empty($_GPC['search'])){
                $where['real_name like'] = "%".$_GPC['search']."%";
            }

            // 是否有按字段排序
            if(!empty($_GPC['field'])){
                $field = $_GPC['field'];
                $order = $_GPC['order'];
            } else {
                $field = 'sort';
                $order = 'desc';
            }

            // 分页
            $page = $_GPC['page'];
            $limit = $_GPC['limit'];

            // 公用条件
            $where['is_del'] = 1;
            $where['active_id'] = $_GPC['aid'];
            $activeList = $this->query->from($this->part)->select('id', 'nick_name', 'real_name',  'gender', 'avatar_url', 'is_show', 'sort', 'votes', 'active_id')->where($where)->orderby($field, $order)->page($page, $limit)->getall();
            foreach($activeList as $key => $value){
                if(!empty($value['real_name'])){
                    $activeList[$key]['name'] = $value['real_name'];
                } else {
                    $activeList[$key]['name'] = $value['nick_name'];
                }
            }
            $webData['data'] = $activeList;
            $webData['count'] = $this->query->from($this->part)->select('id')->where($where)->count();
            $webData['code'] = 0;
        }

        return json_encode($webData);
    }

    // 参与者详情页
    public function doWebAddParticipant()
    {
        global $_W, $_GPC;

        if (!empty($_GPC['id'])){
            $uid = $_GPC['id'];
        } else {
            // 跳转错误页面
        }

        if($_GPC['type'] == 1){
            // 添加参与者
            $type = 1;
            $aid = $_GPC['aid'];
        } elseif($_GPC['type'] == 2){
            // 编辑或查看参与者
            $type = 2;
            $data = $this->query->from($this->part)->select('id', 'nick_name', 'real_name', 'age', 'gender', 'introduction', 'avatar_url', 'video_url', 'add_operator', 'add_time', 'is_show', 'sort', 'active_id')->where(array("id" => $uid))->get();
            if(!empty($data)){
                $data['add_time'] = date('Y-m-d H:i:s', $data['add_time']);
            }
        }

        include $this->template('editParticipant');
    }

    // 编辑参与者
    public function doWebAddOrUpdateParticipant()
    {
        global $_W, $_GPC;
        if (empty($_GPC['nick_name']) && empty($_GPC['real_name'])){
            $webData['code'] = 2002;
            $webData['errorMsg'] = "请填写至少一种名称";
        } else if (empty($_GPC['avatar_url'])){
            $webData['code'] = 2003;
            $webData['errorMsg'] = "请上传头像";
        } else {
            if($_GPC['is_show'] == 'on'){
                $data['is_show'] = 1;
            } else {
                $data['is_show'] = 2;
            }

            $data['nick_name'] = $_GPC['nick_name'];
            $data['real_name'] = $_GPC['real_name'];
            $data['video_url'] = $_GPC['video_url'];
            $data['avatar_url'] = $_GPC['avatar_url'];
            $data['introduction'] = $_GPC['introduction'];
            $data['age'] = $_GPC['age'];
            $data['sort'] = $_GPC['sort'];
            $data['gender'] = $_GPC['gender'];

            if(empty($_GPC['type'])){
                $webData['code'] = 2001;
                $webData['errorMsg'] = "方式有误 错误码: 2001";
            } else if ($_GPC['type'] == 1){
                if (!empty($_GPC['aid'])){
                    $data['active_id'] = $_GPC['aid'];
                    $data['add_time'] = time();
                    $data['add_operator'] = $_W['username'];
                    // 插入 数据库
                    $res = pdo_insert('wx_'.$this->part, $data);
                    if (empty($res)){
                        $webData['code'] = 201;
                        $webData['errorMsg'] = "添加失败, 请重试或联系管理员...";
                    } else{
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "添加成功!";
                    }
                } else {
                    $webData['code'] = 2005;
                    $webData['errorMsg'] = "添加失败, 错误码: 2005";
                }
            } else if ($_GPC['type'] == 2){
                // 编辑
                $where['id'] = $_GPC['id'];
                $res = pdo_update('wx_'.$this->part, $data, $where);
                if (empty($res)){
                    $webData['code'] = 201;
                    $webData['errorMsg'] = "修改失败或没有任何修改";
                } else{
                    $webData['code'] = 200;
                    $webData['errorMsg'] = "修改成功!";
                }
            } else {
                $webData['code'] = 2004;
                $webData['errorMsg'] = "参数有误 错误码: 2004";
            }
        }

        return json_encode($webData);

    }

    // 医院管理
    public function doWebHospital()
    {
        global $_W, $_GPC;
        //todo 账号验证

        /*if ($_W['uid'] === 1){
            include $this->template('hospital');
        } else {
            include $this->template('error');
        }*/
        include $this->template('hospital');
    }

    public function doWebEditOrUpdateHospital()
    {
        global $_W, $_GPC;
        if($_GPC['type'] == 1){
            $type = 1;

        } else if($_GPC['type'] == 2) {
            $type = 2;
            $where['id'] = $_GPC['id'];
            $data = $this->query->from($this->hospital)->select('id', 'title', 'add_time', 'add_operator', 'is_show', 'sort')->where($where)->get();
            $data['add_time'] = date("Y-m-d H:i:s", $data['add_time']);
        }
        include $this->template('editHospital');
    }

    public function doWebAddOrUpdateHospital()
    {
        global $_W, $_GPC;

        if(empty($_GPC['type'])){
            $webData['code'] = 2001;
            $webData['errorMsg'] = "参数有误 错误码: 2001";
        }else{
            if($_GPC['title'] == null || $_GPC['title'] == ''){
                $webData['code'] = 2002;
                $webData['errorMsg'] = '医院名不能为空';
            } else{
                $data['title'] = $_GPC['title'];
                if($_GPC['sort'] != null && $_GPC['sort'] != ''){
                    $data['sort'] = $_GPC['sort'];
                } else {
                    $data['sort'] = 0;
                }

                if($_GPC['is_show'] == 'on'){
                    $data['is_show'] = 1;
                } else{
                    $data['is_show'] = 2;
                }
                if($_GPC['type'] == 1){
                    $data['add_time'] = time();
                    $data['add_operator'] = $_W['username'];
                    $data['is_del'] = 1;

                    $res = pdo_insert("wx_".$this->hospital, $data);
                    if (empty($res)){
                        $webData['code'] = 201;
                        $webData['errorMsg'] = "添加失败";
                    } else {
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "添加成功";
                    }
                } else if($_GPC['type'] == 2) {

                    if (empty($_GPC['id'])){
                        $webData['code'] = 2004;
                        $webData['errorMsg'] = "参数有误 错误码: 2004";
                    } else {
                        $where['id'] = $_GPC['id'];
                        $res = pdo_update("wx_".$this->hospital, $data, $where);
                        if(empty($res)){
                            $webData['code'] = 201;
                            $webData['errorMsg'] = "修改失败";
                        } else {
                            $webData['code'] = 200;
                            $webData['errorMsg'] = "修改成功";
                        }
                    }
                } else{
                    $webData['code'] = 2003;
                    $webData['errorMsg'] = "参数有误 错误码: 2003";
                }
            }
        }

        return json_encode($webData);

    }
    // 获取医院列表
    /*public function doWebGetHospitalInfo()
    {
        global $_W, $_GPC;

        if (!empty($_GPC['search'])){
            $where['h.title like'] = "%".$_GPC['search']."%";
        }

        if (!empty($_GPC['field'])){
            $field = "h.".$_GPC['field'];
        } else {
            $field = "h.id";

        }

        if (!empty($_GPC['order'])){
            $order = $_GPC['order'];
        } else {
            $order = 'asc';
        }

        $page = $_GPC['page'];
        $limit = $_GPC['limit'];
        $where['is_del'] = 1;
        $hospitals = $this->query->from($this->hospital, 'h')->leftjoin($this->hos_user, 'r')->on('h.id', 'r.hid')->select('h.id', 'h.title', 'h.add_time', 'h.is_show', 'h.sort', 'r.uid')->where($where)->page($page,$limit)->orderby($field, $order)->getall();
        $count = $this->query->from($this->hospital)->select('id')->where($where)->count();
        foreach ($hospitals as $key => $value){
            $hospitals[$key]['add_time']  = date('Y-m-d', $value['add_time']);
            if(!empty($value['uid'])){
                $hospitals[$key]['title'] = $value['title']." <img src='https://weixin.prykweb.com/addons/vote_active/res/comment/icon/lock.png' width='16px' height='16px'>";
            }
        }

        $webData['data'] = $hospitals;
        $webData['count'] = $count;
        $webData['code'] = 0;
        return json_encode($webData);
    }*/
    public function doWebGetHospitalInfo()
    {
        global $_W, $_GPC;

        if (!empty($_GPC['search'])){
            $where['title like'] = "%".$_GPC['search']."%";
        }

        if (!empty($_GPC['field'])){
            $field = $_GPC['field'];
        } else {
            $field = "id";

        }

        if (!empty($_GPC['order'])){
            $order = $_GPC['order'];
        } else {
            $order = 'asc';
        }

        $page = $_GPC['page'];
        $limit = $_GPC['limit'];
        $where['is_del'] = 1;
        $hospitals = $this->query->from($this->hospital)->select('id', 'title', 'add_time', 'is_show', 'sort')->where($where)->page($page,$limit)->orderby($field, $order)->getall();
        $count = $this->query->from($this->hospital)->select('id')->where($where)->count();
        foreach ($hospitals as $key => $value){
            $hospitals[$key]['add_time']  = date('Y-m-d', $value['add_time']);
            if(!empty($value['uid'])){
                $hospitals[$key]['title'] = $value['title']." <img src='https://weixin.prykweb.com/addons/vote_active/res/comment/icon/lock.png' width='16px' height='16px'>";
            }
        }

        $webData['data'] = $hospitals;
        $webData['count'] = $count;
        $webData['code'] = 0;
        return json_encode($webData);
    }

    // 权限管理
    public function doWebPermission()
    {
        global $_W, $_GPC;
        include $this->template('permission');
    }

    // 平台账号列表
    public function doWebPlatformAccount()
    {
        global $_W, $_GPC;

        if (!empty($_GPC['search'])){
            $where['username like'] = "%".$_GPC['search']."%";
        }

        if (!empty($_GPC['field'])){
            $field = $_GPC['field'];
        } else {
            $field = "uid";

        }

        if (!empty($_GPC['order'])){
            $order = $_GPC['order'];
        } else {
            $order = 'asc';
        }

        $page = $_GPC['page'];
        $limit = $_GPC['limit'];
        $where['status'] = 2;

        $userList = $this->query->from($this->user)->select('uid', 'username')->where($where)->page($page, $limit)->orderby($field, $order)->getall();
        $count = $this->query->from($this->user)->select('uid', 'username')->where($where)->count();

        $webData['code'] = 0;
        $webData['data'] = $userList;
        $webData['count'] = $count;

        return json_encode($webData);
    }

    // 编辑权限
    public function doWebEditPermission()
    {
        global $_W, $_GPC;
        $uid = $_GPC['uid'];

        // 获取该用户现有权限
        $where['uid'] = $uid;
        $rote = $this->query->from($this->hos_user)->select('hid')->where($where)->get();
        $roteList = explode(',', $rote['hid']);
        // 获取医院列表
        $hosList = $this->query->from($this->hospital)->select('id', 'title')->getall();

        foreach($hosList as $key => $value){
            if (in_array($value['id'], $roteList)){
                $hosList[$key]['selected'] = 1;
            }
        }

        include $this->template('editPermission');
    }

    // 执行编辑权限
    public function doWebDoEditPermission()
    {
        global $_W, $_GPC;
        if(empty($_GPC['uid'])){
            $webData['code'] = 2001;
            $webData['errorMsg'] = "参数错误 错误码: 2001";
        } else {
            $data['hid'] = $_GPC['hid'];
            $where['uid'] = $_GPC['uid'];
            // 判断是否以后关联
            $result = $this->query->from($this->hos_user)->select('hid')->where($where)->get();
            if (empty($result)){
                $data['uid'] = $_GPC['uid'];
                $res = pdo_insert("wx_".$this->hos_user, $data);
            } else {
                $res = pdo_update("wx_".$this->hos_user, $data, $where);
            }
            if(empty($res)){
                $webData['code'] = 201;
                $webData['errorMsg'] = "修改失败";
            } else{
                $webData['code'] = 200;
                $webData['errorMsg'] = "修改成功";
            }
        }

        return json_encode($webData);
    }

    // 投票分析折线图页
    public function doWebActiveResInfo()
    {
        global $_W, $_GPC;
        $aid = $_GPC['aid'];
        // 获取参与者列表
        $where['active_id'] = $aid;
        $list = $this->query->from($this->part)->select('id', 'nick_name', 'real_name', 'is_show', 'is_del')->where($where)->getall();
        include $this->template('detailInfoDiscount');
    }
    // 折线图数据接口
    public function doWebGetLineInfo()
    {
        global $_W, $_GPC;
        $pid = $_GPC['pid'];
        $aid = $_GPC['aid'];
        // 查询游戏开始结束时间
        $aWhere['id'] = $aid;
        $activeInfo = $this->query->from($this->active)->select('id', 'begin', 'end')->where($aWhere)->get();
        // 默认游戏开始 到现在的时间
        if($_GPC['type'] == 2){
            $begin = strtotime($_GPC['begin']);
            $end = strtotime($_GPC['end']);
            if ($begin < $activeInfo['begin']){
                $begin = $activeInfo['begin'];
            } else if($end > $activeInfo['end']){
                $end = $activeInfo['end'];
            }
        } else if ($_GPC['type'] == 1){
            $begin = $activeInfo['begin'];
            $end = $activeInfo['end'];
        }

        if(empty($pid)){
            $whereSql = " ";
        } else{
            $whereSql = " and `pid` = ".$pid;
        }
        $sql = "SELECT count(*) as num , (time div 3600) as t FROM (SELECT * FROM wx_vote_active_action where time >= ".$begin." and time <= ".$end. $whereSql.") a group by t";
        $list = pdo_fetchall($sql);
        // 将二维数组转化为一维 方便后续对不存在的数据做补0
        if(!empty($list)){
            $newList = [];
            foreach ($list as $key => $value){
                $newList[$value['t']] = $value['num'];
            }

            $b = intval($begin/3600);
            $e = ceil($end/3600);
            $cha = $e - $b;
            $data = [];
            for ($i=0; $i<$cha; $i++){
                $k = $b + $i ;
                $data[$i][0] = date('m-d H:i', $k*3600);
                if(!empty($newList[$k])){
                    $data[$i][1] = $newList[$k];
                } else {
                    $data[$i][1] = 0;
                }
            }
            $webData['code'] = 200;
            $webData['data'] = $data;
            $webData['begin'] = date('m-d H:i', $b * 3600);
        } else {
            $webData['code'] = 201;
        }

        return json_encode($webData);

    }

    // 投票分析列表页
    public function doWebActiveResInfo2()
    {
        global $_W, $_GPC;
        $aid = $_GPC['aid'];
        if (!empty($_GPC['pid'])){
            $pid = $_GPC['pid'];
        } else {
            $pid = 0;
        }

        include $this->template('detailInfo');
    }
    // 投票列表数据
    public function doWebGetActiveResInfo()
    {
        global $_W, $_GPC;
        $aid = $_GPC['aid'];
        if (!empty($aid)){
            // 查询投票数据
            $sql = "select a.`id`, a.`pid`, a.`vid`, a.`time`, a.`aid`, v.`nickname`, v.`openid`, v.`headimgurl`, v.`accessIp`, v.`province`, v.`city`,  p.`nick_name`, p.`real_name`, p.`avatar_url` from `wx_".$this->action."` as a left join `wx_".$this->voter."` as v on  a.`vid` = v.`id` left join `wx_".$this->part."` as p  on a.`pid` = p.`id` where a.`aid` = ".$aid;
            if (!empty( $_GPC['id'])){
                $sql = $sql." and a.`id`=".$_GPC['id']." LIMIT 1";
                $list = pdo_fetchall($sql);
                $list[0]['time'] = date('m-d H:i:s', $list[0]['time']);
                $webDate['count'] = 1;
                $webDate['data'] = $list;
                $webDate['code'] = 0;
            } else {
                if(!empty($_GPC['search']) && is_numeric($_GPC['search'])){
                    $sqlSearch = " AND p.`id` =".$_GPC['search'];
                    $sql = $sql.$sqlSearch;
                }else if(!empty($_GPC['search'])){
                    $sqlSearch = " AND p.`nick_name` LIKE '%". $_GPC['search']. "%' or p.`real_name` LIKE '%". $_GPC['search']. "%'";
                    $sql = $sql.$sqlSearch;
                }

                $count = count(pdo_fetchall($sql));
                $orderSql = " order by a.`id` desc ";
                $page = ($_GPC['page'] - 1) * $_GPC['limit'];
                $limit = $_GPC['limit'];
                $sqlLimit = " LIMIT " . $page . "," . $limit;
                $sql = $sql.$orderSql.$sqlLimit;
                $list = pdo_fetchall($sql);
                foreach ($list as $key => $value){
                    $list[$key]['time'] = date('m-d H:i:s', $value['time']);
                    if(!empty($value['nick_name'])){
                        $list[$key]['name'] = $value['nick_name'];
                    } else{
                        $list[$key]['name'] = $value['real_name'];
                    }
                    if(empty($value['province']) && empty($value['city'])){
                        $list[$key]['addr'] = "未知";
                    } else {
                        $list[$key]['addr'] = $value['province'].' '.$value['city'];
                    }
                }
                $webDate['count'] = $count;
                $webDate['data'] = $list;
                $webDate['code'] = 0;
            }
        } else {
            $webDate = [];
        }
        return json_encode($webDate);

    }

    // 获取当前活动总参与人数(去重)
    public function doWebGetNotRepeatingNum()
    {
        global $_W, $_GPC;
        $aid = $_GPC['aid'];
        //todo 判断redis 是否存在
        //todo redis存在则直接获取
        //todo redis不存在 则去数据库计算 并将值存入redis (时间30分钟)
        // 直接获取
        $sql = "select distinct `vid` from `wx_vote_active_action` where `aid`=".$aid ;

        $num = pdo_fetchall($sql);

        if (!empty($num)){
            $webData['code'] = 200;
            $webData['num'] = count($num);
        } else{
            $webData['code'] = 201;
            $webData['errorMsg'] = "未知错误 请重新获取";
        }

        return json_encode($webData);
    }

    public function doWebDetailOne()
    {
        global $_W, $_GPC;
        $aid = $_GPC['aid'];
        $id = $_GPC['id'];
        include $this->template('detailOneInfo');

    }

    // 权限验证
    public function checkAccount($uid)
    {
        if ($uid == 1){
            return true;
        } else {
            return false;
        }
    }
    // 获取二维码
    public function doWebGetCode()
    {
        global $_W, $_GPC;
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        $url = "https://weixin.prykweb.com/weixintest/vote/public/checkIn/".$_GPC['id'];
        $value = $url;
        $pathId = $_GPC['id'];
        $title = $_GPC['title'];
        $filename = IA_ROOT .'/attachment/qrcode/bjhd/'.$pathId.'.png';
        $codeUrl = "https://".substr($filename, 9);
        if (is_file($filename)){
            return " <div style='display: flex;justify-content:space-around'><div><img src= $codeUrl></div><div style
 ='text-align: center;display: flex;flex-direction:column'><p style='margin-top: 20px'>扫描左边二维码</p><a style='margin-top: 10px' href='/attachment/qrcode/$pathId.png' download=$title>下载二维码</a> <input style='border: none;width: 1px;height:1px;outline: none;opacity:0;' type='text' id='copyUrl$pathId' value=$url> <a href='javaScript:;' onclick='copyUrl($pathId)'>复制活动链接</a> </div></div>";
        } else {
            if($value){
                $code = new QRcode;
                $errorCorrectionLevel = 'L';            //容错级别
                $matrixPointSize = 5;                   //生成图片大小
                //生成二维码图片
                $code::png($value, $filename , $errorCorrectionLevel, $matrixPointSize, 2);
                return " <div style='display: flex; justify-content: space-around'><div><img src= $codeUrl></div><div style
 ='text-align: center;display: flex;flex-direction: column'><p style='margin-top: 20px'>扫描左边二维码</p><a style='margin-top: 10px' href='/attachment/qrcode/$pathId.png' download='活动$pathId'>下载二维码</a> <input style='border: none;width: 1px;height:1px;outline: none;opacity: 0;' type='text' id='copyUrl$pathId' value=$url> <a href='javaScript:;' onclick='copyUrl($pathId)'>复制活动链接</a> </div></div> ";
            }
        }
    }
    // 改变sort
    public function doWebChangeSort()
    {
        global $_W, $_GPC;
        if(empty($_GPC['type'])){
            $webData['code'] = 201;
            $webData['errorMsg'] = 'code: 201 errorMsg: 参数丢失';
        }else{
            if ($_GPC['type'] == 1) {
                $table = $this->active;
            } else if ($_GPC['type'] == 2) {
                $table = $this->part;
            } else {
                $table = "";
            }

            $data['sort'] = $_GPC['sort'];
            $where['id'] = $_GPC['id'];
            $request = pdo_update($table, $data, $where);
            if (!empty($request)) {
                $webData['code'] = 200;
                $webData['errorMsg'] = '修改成功!';
            } else {
                $webData['code'] = 202;
                $webData['errorMsg'] = '修改失败!';
            }

        }

        return json_encode($webData);
    }
    // 改变状态
    public function doWebChangeHide()
    {
        global $_W, $_GPC;
        if(empty($_GPC['type'])){
            $webData['code'] = 201;
            $webData['errorMsg'] = 'code: 201 errorMsg: 参数丢失';
        } else {
            if ($_GPC['type'] == 2) {
                $table = $this->part;
            } else if($_GPC['type'] == 3){
                $table = $this->hospital;
            }
            $where['id'] = $_GPC['id'];
            $data['is_show'] = $_GPC['hide'];
            $request = pdo_update($table, $data, $where);
            if (!empty($request)) {
                $webData['code'] = 200;
            } else {
                $webData['code'] = 201;
            }
        }
        return json_encode($webData);
    }
    // 删除
    public function doWebDelInfo()
    {
        global $_W, $_GPC;
        if(empty($_GPC['type'])){
            $webData['code'] = 2001;
            $webData['errorMsg'] = "参数错误 错误码: 2001";
        } else {
            if ($_GPC['type'] == 1){
                $table = $this->active;
            } else if ($_GPC['type'] == 2){
                $table = $this->part;
            } else {
                $table = '';
            }
            $where['id'] = $_GPC['id'];
            $data['is_del'] = 2;
            $request = pdo_update($table, $data, $where);

            if (!empty($request)) {
                $webData['code'] = 200;
                $webData['errorMsg'] = "删除成功";
            } else {
                $webData['code'] = 201;
                $webData['errorMsg'] = "删除失败";
            }
        }

        return json_encode($webData);
    }


    // 回收站
    public function doWebRecycleBin()
    {
        global $_W, $_GPC;
        session_start();
        $uid = $_W['uid'];
        if($uid != 1){
            // 先查询 该用户拥有的医院权限
            $map['uid'] = $uid;
            $hids = $this->query->from($this->hos_user)->select('hid')->where($map)->get();
            $his = explode(',', $hids['hid']);
            $where['id in'] = $his;
        }
        // 获取医院列表
        $where['is_del'] = 1;
        $where['is_show'] = 1;
        $hospitalList = $this->query->from($this->hospital)->select('id', 'title')->where($where)->getall();
        $length = count($hospitalList);
        if(isset($_SESSION['vote_choose_hid_Recycle'])){
            $hid = $_SESSION['vote_choose_hid_Recycle'][$_W['uid']];
        } else {
            $hid = 0;
        }

        include $this->template("recycleBin");
    }

    // 回收活动列表
    public function doWebRecycleActive()
    {
        global $_W, $_GPC;
        session_start();
        if (empty($_GPC['hid'])){
            $webData['data'] = [];
            $webData['count'] = 0;
            $webData['code'] = 0;
        } else {
            // 是否查询字段
            if(!empty($_GPC['search'])){
                $where['title like'] = "%".$_GPC['search']."%";
            }
            // 是否有按字段排序
            if(!empty($_GPC['field'])){
                $field = $_GPC['field'];
                $order = $_GPC['order'];
            } else {
                $field = 'sort';
                $order = 'desc';
            }
            // 分页
            $page = $_GPC['page'];
            $limit = $_GPC['limit'];
            // 公用条件
            $where['is_del'] = 2;
            $where['h_id'] = $_GPC['hid'];
            $activeList = $this->query->from($this->active)->select('id', 'title')->where($where)->orderby($field, $order)->page($page, $limit)->getall();
            //var_dump($this->query->getLastQuery()) ;die();
            $_SESSION['vote_choose_hid_Recycle'] =array( $_W['uid'] => $_GPC['hid']);
            $webData['data'] = $activeList;
            $webData['count'] = $this->query->from($this->active)->select('id')->where($where)->count();
            $webData['code'] = 0;
        }

        return json_encode($webData);
    }

    public function doWebGetActiveLists()
    {
        global $_W, $_GPC;
        if(empty($_GPC['hid'])){
            $webData['code'] = 201;
            $webData['errorMsg'] = "请选择正确的医院";
        } else{
            $where['h_id'] = $_GPC['hid'];
            $data = $this->query->from($this->active)->select('id', 'title', 'is_show', 'is_del')->where($where)->getall();
            if (empty($data)){
                $webData['code'] = 202;
                $webData['errorMsg'] = "暂无活动";
            } else {
                foreach ($data as $key =>$value){
                    if($value['is_del'] == 2){
                        $data[$key]['title'] = $value['title']."(已删除)";
                    } else if($value['is_show'] == 2){
                        $data[$key]['title'] = $value['title']."(已隐藏)";
                    }
                }
                $webData['code'] = 200;
                $webData['data'] = $data;
                $webData['errorMsg'] = "OK";
            }
        }

        return json_encode($webData);
    }

    // 回收参与者列表
    public function doWebRecyclePart()
    {
        global $_W, $_GPC;
        if (empty($_GPC['aid'])){
            $webData['data'] = [];
            $webData['count'] = 0;
            $webData['code'] = 0;
        } else {
            $sql = "select `id`, `nick_name`, `real_name`, `avatar_url` from `wx_vote_active_participant` where `is_del` = 2 AND `active_id` = ".$_GPC['aid'];

            // 是否查询字段
            if(!empty($_GPC['search'])){
                $sql = $sql." AND (`real_name` LIKE '%".$_GPC['search']."%' or `nick_name` LIKE '%".$_GPC['search']."%')  ";
            }
            $count = count(pdo_fetchall($sql));
            // 是否有按字段排序
            if(!empty($_GPC['field'])){
                $orderSql = " order by `".$_GPC['field']."` ".$_GPC['order'];
            } else {
                $orderSql = " order by `sort` desc";
            }
            // 分页
            $page = ($_GPC['page'] - 1) * $_GPC['limit'];
            $limit = $_GPC['limit'];
            $sqlLimit = " LIMIT " . $page . "," . $limit;
            $sql = $sql.$orderSql.$sqlLimit;
            $partList = pdo_fetchall($sql);
            foreach ($partList as $key => $value){
                if(empty($value['real_name'])){
                    $partList[$key]['name'] = $value['nick_name'];
                } else{
                    $partList[$key]['name'] = $value['real_name'];
                }
            }
            $webData['data'] = $partList;
            $webData['count'] = $count;
            $webData['code'] = 0;
        }

        return json_encode($webData);
    }

    // 恢复
    public function doWebRestoreData()
    {
        global $_W, $_GPC;
        if (empty($_GPC['type'])){
            $webData['code'] = 2001;
            $webData['errorMsg'] = "参数错误";
        } else{
            if($_GPC['type'] == 1){
                $table = $this->active;
            } else if ($_GPC['type'] == 2){
                $table = $this->part;
            } else {
                $table = '';
            }
            $where['id'] = (int)$_GPC['id'];
            $data['is_del'] = 1;
            $res = pdo_update($table,$data,$where);

            if (empty($res)){
                $webData['code'] = 201;
                $webData['errorMsg'] = "恢复失败!";
            } else{
                $webData['code'] = 200;
                $webData['errorMsg'] = "恢复成功";
            }
        }

        return json_encode($webData);
    }

    // 彻底删除
    public function doWebRemoveCompletely()
    {
        global $_W, $_GPC;
        if (empty($_GPC['type'])){
            $webData['code'] = 2001;
            $webData['errorMsg'] = "参数错误";
        } else{
            if($_GPC['type'] == 1){
                $table = $this->active;
            } else if ($_GPC['type'] == 2){
                $table = $this->part;
            } else {
                $table = '';
            }
            $where['id'] = $_GPC['id'];
            $res = pdo_delete($table,$where);

            if (empty($res)){
                $webData['code'] = 201;
                $webData['errorMsg'] = "删除失败!";
            } else{
                $webData['code'] = 200;
                $webData['errorMsg'] = "删除成功";
            }
        }

        return json_encode($webData);
    }

    // 帮助模块
    public function doWebHelp()
    {
        global $_W, $_GPC;
        include $this->template('help');
    }
}

