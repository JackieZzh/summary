<?php
/**
 * 便利店模块微站定义
 *
 * @author Gorden
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Gzh_nc_hjsgModuleSite extends WeModuleSite
{
    private $pageCount = 10;//分页每页显示数

    /** 奖品表 */
    private $tb_gamesgoodslist = 'weixin_gamesgoodslist';

    /** 游戏表*/
    private $tb_gameslist = 'weixin_gameslist';

    /** 中间表（哪个用户哪个游戏中啥奖） */
    private $tb_goodslistforgame = 'weixin_goodslistforgame';

    /** 游戏用户中间表 */
    private $tb_useraction = 'weixin_useraction';

    /** 用户信息表 */
    private $tb_userinfo = 'weixin_userinfo';

    /** 游戏用户次数的表*/
    private $tb_usertogamestatus = 'weixin_usertogamestatus';

    public function doWebRule()
    {
        global $_W, $_GPC;
        $rid = intval($_GPC['id']);
        echo $rid;
    }


    /**
     * 获取单品信息
     * @param int $id
     * @return array
     */
    private function getGoods($id)
    {
        global $_W;
        $sql = 'SELECT * FROM ' . tablename($this->tb_goods) . ' WHERE id=:id AND uniacid=:uniacid ';
        $params = array(
            ':id' => intval($id),
            ':uniacid' => $_W['uniacid']
        );
        $goods = pdo_fetch($sql, $params);

        return $goods;
    }

    /**
     * 奖品列表管理
     */
    public function doWebgamesgoodslist()
    {
        global $_GPC, $_W;
        $redisConfig = $_W["config"]['setting']['redis'];
        $redis = new Redis();
        $redis->pconnect($redisConfig['server'], $redisConfig['port'],$redisConfig['timeout']);
        load()->func('tpl');
        $condition = '1';
        if (isset($_GPC['keywords']) && $_GPC['keywords'] != '') {
            $condition .= " and (title like '%" . trim($_GPC['keywords']) . "%' or weight like '%" . trim($_GPC['keywords']) . "%') ";
        }
        $countSql = "select COUNT(*) as total from " . tablename($this->tb_gamesgoodslist) . " where " . $condition;
        $total = pdo_fetch($countSql);
        if (!isset($_GPC['page'])) {
            $pageindex = 1;
        } else {
            $pageindex = intval($_GPC['page']);
        }
        $pagesize = $this->pageCount;
        $pager = pagination($total['total'], $pageindex, $pagesize);
        $sql = "select * from " . tablename($this->tb_gamesgoodslist) . " where " . $condition . " order by id desc limit " . ($pageindex - 1) * $pagesize . "," . ($pagesize);
        $list = pdo_fetchall($sql);
        echo "<pre>";
        print_r($list);
        echo "</pre>";die;
        $redisConfig = $_W["config"]['setting']['redis'];
        $redis = new Redis();
        $redis->pconnect($redisConfig['server'], $redisConfig['port'],$redisConfig['timeout']);

        foreach( $list as $key => $value ){
            $res["id"] = $value["id"];
            $res["num"] = $this->redis->hGet("luckyGame".$gid, $value["id"]);
            array_push($arr,$res);
        }

        /*echo "<pre>";
        var_dump($list);
        echo "</pre>";die;*/
        include $this->template('gamesgoodslist');
    }


    /**
     * 添加奖品
     */
    public function doWebAddGameGoods()
    {
        global $_GPC;
        $game_id = $_GPC['game_id'];
        include $this->template("games_goods_list_add");
    }

    /**
     * 执行添加奖品
     */
    public function doWebDoAddGameGoods()
    {
        global $_GPC;
        $game_id = $_GPC['game_id'];
        $data = array(
            'title' => trim($_GPC['title']),
            'picurl' => "https://weixin.prykweb.com/attachment/".$_GPC['picurl'],
            'weight' => $_GPC['weight'],
            'num' => strip_tags(trim($_GPC['num'])),
            'gid' => $_GPC['game_id']
        );
        $result = pdo_insert($this->tb_gamesgoodslist, $data);
        if (!empty($result)) {
            message('添加成功', "https://weixin.prykweb.com/web/index.php?c=site&a=entry&do=getGameGoods&m=gzh_nc_hjsg&op=getGameGoods&id=".$game_id."&game_id=".$game_id);
        } else {
            message('添加失败', '', "warning");
        }
    }
    /**
     * 奖品删除
     */
    public function doWebgamesgoodslistdel()
    {
        global $_GPC, $_W;
        $request = pdo_delete($this->tb_gamesgoodslist, array('id' => $_GPC['id']));
        if ($request) {
            $json = array('status' => 1, 'msg' => '操作成功');
            echo json_encode($json);
        } else {
            $json = array('status' => 0, 'msg' => '操作失败');
            echo json_encode($json);
        }
    }
    /**
     * 奖品编辑
     */
    public function doWebgamesgoodslistedit()
    {
        global $_GPC, $_W;
        $id = $_GPC['id'];
        $game_id = $_GPC['game_id'];
        $info = pdo_fetch("SELECT * FROM " . tablename($this->tb_gamesgoodslist) . " WHERE id = :id", array(':id' => $id));
        include $this->template("gamesgoodslistedit");
    }
    /**
     * 处理编辑奖品的逻辑
     */
    public function doWebgamesgoodslistupdate()
    {
        global $_GPC, $_W;
        $id = $_GPC['id'];
        $game_id = $_GPC['game_id'];
        $data = array(
            'title' => trim($_GPC['title']),
            'picurl' => "https://weixin.prykweb.com/attachment/".$_GPC['picurl'],
            'weight' => $_GPC['weight'],
            'num' => strip_tags(trim($_GPC['num'])),
            'id' => $_GPC['id']
        );
        $result = pdo_update($this->tb_gamesgoodslist, $data, array('id' => $id));
        if (!empty($result)) {
            message('更新成功', "https://weixin.prykweb.com/web/index.php?c=site&a=entry&do=getGameGoods&m=gzh_nc_hjsg&op=getGameGoods&id=".$game_id."&game_id=".$game_id);
        } else {
            message('更新失败', '', "warning");
        }
    }

    /**
     * 用户列表管理
     */
    public function doWebuserinfo()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $query = load()->object('query');
        // 获取当前活动列表
        $active = $query->from($this->tb_gameslist)->select('id', 'title')->getall();
        // 查询当前是否有医院session
        session_start();
        $condition = '1';

        if ($_GPC['aid'] == '0') {
            $_SESSION['the_lucky_roller_choose_active'] = 0;
        }
        if (isset($_GPC['aid']) && $_GPC['aid'] != '') {
            $_SESSION['the_lucky_roller_choose_active'] = $_GPC['aid'];
            if ($_GPC['aid'] != '0') {
                $condition .= " and `aid` = ".$_GPC['aid'];
                $isChoose = $_GPC['aid'];
            } else {
                $isChoose = 0;
            }

        } elseif ($_SESSION['the_lucky_roller_choose_active']) {
            $condition .= " and `aid` = ".$_SESSION['the_lucky_roller_choose_active'];
            $isChoose = $_SESSION['the_lucky_roller_choose_active'];
        }


        if (isset($_GPC['keywords']) && $_GPC['keywords'] != '') {
            $condition .= " and (share like '%" . trim($_GPC['keywords']) . "%' ) ";
        }
        $countSql = "select COUNT(*) as total from " . tablename($this->tb_userinfo) . " where " . $condition;
        $total = pdo_fetch($countSql);
        if (!isset($_GPC['page'])) {
            $pageindex = 1;
        } else {
            $pageindex = intval($_GPC['page']);
        }
        $pagesize = $this->pageCount;
        $sql = "select * from " . tablename($this->tb_userinfo) . " where " . $condition . " order by id desc limit " . ($pageindex - 1) * $pagesize . "," . ($pagesize);
        $pager = pagination($total['total'], $pageindex, $pagesize);
        $list = pdo_fetchall($sql);

        include $this->template('userinfo');
    }

    /**
     * 人员删除
     */
    public function doWebdeluserinfo()
    {
        global $_GPC, $_W;
        $request = pdo_delete($this->tb_userinfo, array('id' => $_GPC['id']));
        if ($request) {
            $json = array('status' => 1, 'msg' => '操作成功');
            echo json_encode($json);
        } else {
            $json = array('status' => 0, 'msg' => '操作失败');
            echo json_encode($json);
        }
    }

    /**
     * 游戏列表
     */
    public function doWebgameslist()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $condition = array();
        $condition = '1';
        if (isset($_GPC['keywords']) && $_GPC['keywords'] != '') {
            $condition .= " and (title like '%" . trim($_GPC['keywords']) . "%' or whichunit like '%" . trim($_GPC['keywords']) . "%') ";
        }
        $countSql = "select COUNT(*) as total from " . tablename($this->tb_gameslist) . " where " . $condition;
        $total = pdo_fetch($countSql);
        if (!isset($_GPC['page'])) {
            $pageindex = 1;
        } else {
            $pageindex = intval($_GPC['page']);
        }
        $pagesize = $this->pageCount;
        $pager = pagination($total['total'], $pageindex, $pagesize);

        $sql = "select * from " . tablename($this->tb_gameslist) . " where " . $condition . " order by id desc limit " . ($pageindex - 1) * $pagesize . "," . ($pagesize);
        $list = pdo_fetchall($sql);
        include $this->template('gameslist');
    }

    /**
     * 获取游戏状态描述
     * @param int $status
     * @return string
     */
    private function getGamesStatus($status)
    {
        $status = intval($status);
        if ($status == 1) {
            $str = "<span class='btn btn-success radius'>开启</span>";
        } elseif ($status == 0) {
            $str = "<span class='btn btn-danger radius'>未开启</span>";
        }
        return $str;
    }

    public function doWebAddGamesList()
    {
        include $this->template('add_game');
    }

    public function doWebDoAddGame()
    {
        global $_GPC;
        $data = array(
            'title' => trim($_GPC['title']),
            'usertimes' => $_GPC['usertimes'],
            'contact' => strip_tags($_GPC['contact']),
            'status' => $_GPC['status'],
            'expirestime' => strtotime($_GPC['expirestime']),
            'whichunit' => strip_tags($_GPC['whichunit']),
            'createtime' => time()
        );
        $res = pdo_insert($this->tb_gameslist, $data);
        if ($res){
            message('添加成功', $this->createWebUrl('gameslist'));
        } else {
            message('添加失败', '', "warning");
        }
    }

    /**
     * 获取活动奖品信息
     */
    public function doWebGetGameGoods()
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $game_id = $_GPC['game_id'];
        $condition = " `gid` = ". $game_id;
        if (isset($_GPC['keywords']) && $_GPC['keywords'] != '') {
            $condition .= " and (title like '%" . trim($_GPC['keywords']) . "%' or weight like '%" . trim($_GPC['keywords']) . "%') ";
        }
        $countSql = "select COUNT(*) as total from " . tablename($this->tb_gamesgoodslist) . " where " . $condition;
        $total = pdo_fetch($countSql);
        if (!isset($_GPC['page'])) {
            $pageindex = 1;
        } else {
            $pageindex = intval($_GPC['page']);
        }
        $pagesize = $this->pageCount;
        $pager = pagination($total['total'], $pageindex, $pagesize);
        $sql = "select * from " . tablename($this->tb_gamesgoodslist) . " where " . $condition . " order by id desc limit " . ($pageindex - 1) * $pagesize . "," . ($pagesize);
        $list = pdo_fetchall($sql);

        $redisConfig = $_W["config"]['setting']['redis'];
        $redis = new Redis();
        $redis->pconnect($redisConfig['server'], $redisConfig['port'],$redisConfig['timeout']);

        $redis_num = $redis->exists('luckyGame'.$game_id);

        if ($redis_num == 1){
            foreach( $list as $key => $value ){
                $list[$key]["redis_num"] = $redis->hGet("luckyGame".$game_id, $value["id"]);
            }
        }
        /*echo "<pre>";
        var_dump($list);
        echo "</pre>";die;*/
        include $this->template('gamesgoodslist');
    }

    /**
     * 游戏列表编辑
     */
    public function doWebgameslistedit()
    {
        global $_GPC, $_W;
        $id = $_GPC['id'];
        $game_id = $_GPC['game_id'];
        $info = pdo_fetch("SELECT * FROM " . tablename($this->tb_gameslist) . " WHERE id = :id", array(':id' => $id));
        include $this->template("gameslistedit");
    }

    /**
     * 游戏列表编辑逻辑
     */
    public function doWebgameslistupdate()
    {
        global $_GPC, $_W;
        $id = $_GPC['id'];
        $data = array(
            'title' => trim($_GPC['title']),
            'usertimes' => $_GPC['usertimes'],
            'contact' => strip_tags($_GPC['contact']),
            'status' => $_GPC['status'],
            'expirestime' => strtotime($_GPC['expirestime']),
            'whichunit' => strip_tags(trim($_GPC['whichunit'])),
            'id' => $_GPC['id']
        );
        $result = pdo_update($this->tb_gameslist, $data, array('id' => $id));
        if (!empty($result)) {
            message('更新成功', $this->createWebUrl('gameslist'));
        } else {
            message('更新失败', '', "warning");
        }

    }

    /**
     * 中奖信息
     */
    public function doWebwinninginfo()
    {
        global $_GPC, $_W;
        //$this->P($_GPC);
        $citys = $this->getCitys();
        $condition = '1';
        if (isset($_GPC['keywords']) && $_GPC['keywords'] != '') {
            $condition .= " and (nickname like '%" . trim($_GPC['keywords']) . "%' or d.title like '%" . trim($_GPC['keywords']) . "%' or b.tel like '%" . trim($_GPC['keywords']) . "%') ";
        }
        // 1.一进来的时候默认的是 0，此时设置cooike值为0放后边sql语句
        if ($_GPC['city'] == '0') {
            setcookie('citycooike', '0');
        }
        // 2.当选择了下拉框时设置cooike（此时要排除获取option为0的情况）
        if (isset($_GPC['city']) && $_GPC['city'] != '') {
            setcookie('citycooike', $_GPC['city']);
            if ($_GPC['city'] != '0') {
                $condition .= " and (c.id = " . trim($_GPC['city']) . ")";
            }
            // 3.当cooike设置值的时候 将cooike的值默认赋值给get的值
        } elseif ($_COOKIE['citycooike']) {
            $condition .= " and (c.id = " . $_COOKIE['citycooike'] . ")";
            $_GPC['city'] = $_COOKIE['citycooike'];
        }


        $countSql = $this->getSql($_GPC['city'], "a.* ,b.* ,c.*,d.id,d.title as goodsname,d.gid,d.picurl,d.weight,d.num");
        $total = count(pdo_fetchall($countSql));
        //获取页数
        $pageindex = max(1, intval($_GPC['page']));
        //获取页行数
        $pagesize = $this->pageCount;
        $pager = pagination($total, $pageindex, $pagesize);
        $sql = $this->getSql($_GPC['city'], "a.* ,b.* ,c.*,d.id,d.title as goodsname,d.gid,d.picurl,d.weight,d.num") . ' limit ' . ($pageindex - 1) * $pagesize . "," . ($pagesize);
        $list = pdo_fetchall($sql);

        foreach ($list as $key => $value){
            if($value['ip']){
                $ips = $this->getIpSite($value['ip']);
                $list[$key]['ip'] = $ips['pro'].$ips['city'];
            } else {
                $list[$key]['ip'] = "";
            }
        }

        include $this->template("winiinginfo");

    }


    /**
     * 获取中奖信息
     */
    public function doWebGetWinningInfo()
    {
        global $_GPC;

    }

    /**
     * 获取城市
     */
    private function getCitys()
    {
        global $_W;
        $sql = 'SELECT `*` FROM ' . tablename($this->tb_gameslist);
        $citys = pdo_fetchall($sql);
        $data = array();
        foreach ($citys as $k => $v) {
            $data[$v['id']] = $v['whichunit'];
        }
        return $citys;
    }

    /**
     * 导出医院execl数据
     */
    public function doWebexport()
    {
        global $_GPC, $_W;
        //定义表头
        $headList = array("用户昵称", "真实姓名", "电话", 'IP归属地', "奖品名称", '手机归属地');
        // 根据下拉框获取的城市id获取sql语句
        $countSql = $this->getSql($_GPC['city'], " b.nickname,b.realname,b.tel,b.ip ,d.title as goodsname");
        $dataList = pdo_fetchall($countSql);
        foreach ($dataList as $k => &$val) {
            $dataList[$k]['location'] = $this->getLocationByTel($val['tel']);
            if($val['ip']){
                $ips = $this->getIpSite($val['ip']);
                $dataList[$k]['ip'] = $ips['pro'].$ips['city'];
            } else {
                $dataList[$k]['ip'] = '未知';
            }

            $val['nickname'] = addslashes($val['nickname']);
        }
        $sql = "select whichunit from wx_weixin_gameslist where id = {$_GPC['city']}";
        $res = pdo_fetchall($sql);
        $res = $res[0]['whichunit'];
        //定义导出的文件名
        $fileName = "$res" . date("Ymd", time()) . "_" . rand(1, 100);
        $this->exportexcel($dataList, $headList, $fileName);
    }

    /* 
    * $tel    string  手机号
    * 根据手机号获取手机号归属地
    */
    public function getLocationByTel($tel)
    {
        if (!$tel) {
            $str = '';
        } else {
            $res = file_get_contents("http://mobsec-dianhua.baidu.com/dianhua_api/open/location?tel=$tel");
            $res = json_decode($res, true);
            $str = $res['response'][$tel]['location'];
        }

        return $str;
    }

    /*
    *  导出execl功能
    * $data       array   要导出的数据
    * $title      array   导出的表头
    * $filename   string  导出的文件名
    */

    public function exportexcel($data = array(), $title = array(), $filename)
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);
            }
            echo implode("\n", $data);
        }
    }


    /**获取sql语句 根据城市id和字段名称
     * @param $city  城市id
     * @param $field 字段
     */
    public function getSql($city, $field)
    {
        if ($city) {
            $flag = "and c.id =  {$city} ";
        } else {
            if ($_COOKIE['citycooike']) {
                $flag = "and (c.id = " . $_COOKIE['citycooike'] . ")";
            } else {
                $flag = ' ';
            }
        }
        $countSql = "select  $field from " . tablename('weixin_goodslistforgame') . " as a INNER JOIN " . tablename('weixin_userinfo') . "  as b on a.uid =  b.id INNER JOIN  " . tablename('weixin_gameslist') . "  as c on a.gid = c.id INNER JOIN " . tablename('weixin_gamesgoodslist') . "  as d on a.goodsid = d.id " . $flag . " order by b.id desc";
        return $countSql;
    }

    private function getIpSite($ip)
    {
        $content = $this->doCurlGetRequest("http://whois.pconline.com.cn/ipJson.jsp?json=true&ip={$ip}", 5);
        if ($content === false)
            return '未知登陆地区';
        $json = json_decode(mb_convert_encoding($content, 'UTF-8', 'GBK'), true);
        return $json;
    }

    /**
     * @param $url
     * @param $timeout
     * @return bool|mixed
     * curl get方法
     */
    private function doCurlGetRequest($url,$timeout){
        if($url == "" || $timeout <= 0){
            return false;
        }
        $con = curl_init($url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
        $data = curl_exec($con);
        curl_close($con);
        return $data;
    }


    public function P($data, $type = false)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        !$type ? die : true;
    }

}