<?php
/**
 *  普瑞人期刊模块
 */
defined('IN_IA') or exit('Access Denied');
require_once('res/comment/upload/Upload.php');

class PruirModuleSite extends WeModuleSite
{
    /**
     *  文章表
     * @var string
     */
    private $tb_article = "prr_article";
    /**
     *  文章期刊表
     * @var string
     */
    private $tb_article_periodical = "prr_article_periodical";
    /**
     *  文章分类表
     * @var string
     */
    private $tb_article_category = "prr_article_category";
    /**
     * banner 表
     * @var string
     */
    private $tb_banner = "prr_banner";

    public function doWebRule()
    {
        global $_W, $_GPC;
        $rid = intval($_GPC['id']);
        echo $rid;
    }

    /*banner管理*/
    public function doWebBanner()
    {
        global $_W, $_GPC;
        include $this->template('banner');
    }

    public function doWebGetBanner()
    {
        global $_GPC, $_W;
        $sql = "SELECT `id`, `sort`, `image`, `title`, `hidden` FROM `wx_prr_banner` WHERE `uniacid` = 19 AND `is_del` = 0 ";
        if (!empty($_GPC['order'])) {
            $order = $_GPC['order'];
        } else {
            $order = 'desc';
        }
        if (!empty($_GPC['field'])) {
            $field = $_GPC['field'];
        } else {
            $field = 'sort';
        }
        $sql1 = " ORDER BY " . $field . " " . $order;
        if (!empty($_GPC['title'])) {
            $title = $_GPC['title'];
            $sql2 = " AND `title` LIKE '%" . $title . "%'";
            $sql = $sql . $sql2;
        }
        $sql = $sql . $sql1;
        $count = count(pdo_fetchall($sql));
        $page = ($_GPC['page'] - 1) * $_GPC['limit'];
        $limit = $_GPC['limit'];
        $sql3 = "LIMIT " . $page . "," . $limit;
        $sql = $sql . " " . $sql3;
        $list = pdo_fetchall($sql);

        $webData['data'] = $list;
        $webData['code'] = 0;
        $webData['count'] = $count;

        return json_encode($webData);

    }

    public function doWebAddOrUpdateBanner()
    {
        global $_W, $_GPC;
        $type = $_GPC['type'];
        $data['title'] = $_GPC['title'];
        $data['sort'] = $_GPC['sort'];
        $data['url'] = $_GPC['url'];
        //$data['image'] = $_GPC['banner'];
        $data['hidden'] = $_GPC['hide'];

        if(($_GPC['banner'] === null || $_GPC['banner'] == "") &&  $type != 2){
            $webData['code'] = 40001;
            $webData['errMsg'] = "请上传banner图";
        } else{
            if((int)$type === 1){
                $data['add_time'] = time();
                $data['uniacid'] = $_W['uniacid'];
                $data['image'] = '/attachment/' . $_GPC['banner'];
                $request = pdo_insert($this->tb_banner, $data);
                if (!empty($request)) {
                    $webData['code'] = 200;
                    $webData['errorMsg'] = "添加成功";

                } else {
                    $webData['code'] = 202;
                    $webData['errorMsg'] = "添加失败";
                }
            } elseif((int)$type === 2) {
                if ($_GPC['bid']){
                    $where['id'] = $_GPC['bid'];
                    $where['uniacid'] = $_W['uniacid'];
                    if($_GPC['banner']){
                        $data['image'] = '/attachment/' . $_GPC['banner'];
                    }
                    $request = pdo_update($this->tb_banner, $data, $where);
                    if (!empty($request)) {
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "修改成功";

                    } else {
                        $webData['code'] = 202;
                        $webData['errorMsg'] = "修改失败";
                    }
                } else{
                    $webData['code'] = 40003;
                    $webData['errMsg'] = "id参数有误";
                }
            }else{
                $webData['code'] = 40002;
                $webData['errMsg'] = "type参数有误";
            }
        }

        return json_encode($webData);
    }

    public function doWebDelBanner()
    {
        global $_W, $_GPC;
        $where['id'] = $_GPC['id'];
        $where['uniacid'] =$_W['uniacid'];

        $data['is_del'] = 1;
        $request = pdo_update($this->tb_banner, $data, $where);
        if ($request > 0) {
            $webData['code'] = 200;
            $webData['errorMsg'] = "删除成功";

        } else {
            $webData['code'] = 202;
            $webData['errorMsg'] = "删除失败";
        }
        return json_encode($webData);
    }
    /**
     * 期刊管理
     */
    /**
     * 期刊列表页
     */
    public function doWebPeriodical()
    {
        global $_W, $_GPC;
        include $this->template('periodical');
    }

    /**
     * 获取期刊列表
     * @return false|string
     */
    public function doWebGetPeriodicalList()
    {
        global $_GPC, $_W;
        if (!empty($_GPC['field'])) {
            $field = $_GPC['field'];
        } else {
            $field = 'sort';
        }
        if (!empty($_GPC['order'])) {
            $order = $_GPC['order'];
        } else {
            $order = 'desc';
        }
        $query = load()->object('query');
        $count = $query->from('prr_article_periodical')->where(array('uniacid' => $_W['uniacid'], 'is_del' => 0))->count();
        $page = $_GPC['page'];
        $limit = $_GPC['limit'];
        $list = $query->from('prr_article_periodical')->where(array('uniacid' => $_W['uniacid'], 'is_del' => 0))->page($page, $limit)->orderby($field, $order)->getall();
        $listLen = count($list);
        if ($limit > $listLen) {
            $num = $listLen;
        } else {
            $num = $limit;
        }
        for ($i = 0; $i < $num; $i++) {
            if ($list[$i]['hidden'] == 0) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            $str1 = "<a title='编辑' class='layui-btn layui-btn-sm' onclick='editPer(" . json_encode($list[$i]) . ")'><i class='layui-icon'>&#xe642;</i></a><a title='删除' class='layui-btn layui-btn-sm' onclick='delPer(" . $list[$i]['id'] . ")'>
<i class='layui-icon'>&#xe640;</i></a>";
            $str2 = "<image src='" . $list[$i]['image'] . "' width='40px' height='40px' class='asdf'></image>";
            $str3 = '<input type="checkbox" value=' . $list[$i]['hidden'] . ' data_id=' . $list[$i]['id'] . ' lay-skin="switch" lay-filter="hideStatus" lay-text="已显示|已隐藏" ' . $checked . '>';
            $str4 = '<input type="text" id="changeSort' . $list[$i]['id'] . '" data-id=' . $list[$i]['id'] . ' style="width:100%; height: 90%; padding-left:40%; padding-right:10%" onblur="changeSort(' . $list[$i]['id'] . ')" name="sort" value="' . $list[$i]['sort'] . '">';
            $list[$i]['operating'] = $str1;
            $list[$i]['image'] = $str2;
            $list[$i]['hidden'] = $str3;
            $list[$i]['sort'] = $str4;
        }
        $webData['data'] = $list;
        $webData['code'] = 0;
        $webData['count'] = $count;


        return json_encode($webData);
    }

    /**
     * 添加 / 编辑期刊
     * @return false|string
     */
    public function doWebAddOrUpdatePeriodical()
    {
        global $_GPC, $_W;
        $type = $_GPC['type'];
        $data['title'] = $_GPC['title'];
        $data['intr'] = $_GPC['intr'];
        $data['sort'] = $_GPC['sort'];
        $data['hidden'] = $_GPC['hidden'];
        $data['uniacid'] = $_W['uniacid'];
        if ($type == 1) {
            // 新增
            // 插入期刊数据
            $data['image'] = '/attachment/' . $_GPC['face'];
            $data['add_time'] = time();
            $request = pdo_insert($this->tb_article_periodical, $data);
            if (!empty($request)) {
                $webData['code'] = 200;
                $webData['errorMsg'] = "添加成功";

            } else {
                $webData['code'] = 201;
                $webData['errorMsg'] = "添加失败";
            }
        } elseif ($type == 2) {
            if ($_GPC['porId']) {
                // 更新
                if ($_GPC['face']) {
                    $data['image'] = '/attachment/' . $_GPC['face'];
                }
                $request = pdo_update($this->tb_article_periodical, $data, array('id' => $_GPC['porId'], 'uniacid' => $_W['uniacid']));
                if (!empty($request)) {
                    $webData['code'] = 200;
                    $webData['errorMsg'] = "修改成功";

                } else {
                    $webData['code'] = 202;
                    $webData['errorMsg'] = "修改失败";
                }
            } else {
                $webData['code'] = 204;
                $webData['errorMsg'] = "非法参数";
            }
        } else {
            $webData['code'] = 203;
            $webData['errorMsg'] = "非法参数";
        }

        return json_encode($webData);
    }

    /**
     * 删除期刊
     * @return false|string
     */
    public function doWebDelPeriodical()
    {
        global $_GPC, $_W;
        $where['uniacid'] = $_W['uniacid'];
        $where['id'] = $_GPC['id'];

        $data['is_del'] = 1;
        $request = pdo_update($this->tb_article_periodical, $data, $where);
        if ($request > 0) {
            $webData['code'] = 200;
            $webData['errorMsg'] = "删除成功";

        } else {
            $webData['code'] = 202;
            $webData['errorMsg'] = "删除失败";
        }

        return json_encode($webData);
    }

    /**
     * 分类管理
     */
    /**
     * 分类列表页
     */
    public function doWebCategory()
    {
        global $_W, $_GPC;
        include $this->template('category');
    }

    /**
     * 获取分类列表
     * @return false|string
     */
    public function doWebGetCategoryList()
    {
        global $_GPC, $_W;
        if (!empty($_GPC['field'])) {
            $field = $_GPC['field'];
        } else {
            $field = 'sort';
        }
        if (!empty($_GPC['order'])) {
            $order = $_GPC['order'];
        } else {
            $order = 'desc';
        }
        $query = load()->object('query');
        //$count = $query->from('prr_article_category')->where(array('uniacid'=> $_W['uniacid'], 'is_del'=> 0))->count();
        /*$page = $_GPC['page'];
        $limit = $_GPC['limit'];*/
        /*$listLen = count($list);
        if ($limit > $listLen){
            $num = $listLen;
        } else {
            $num = $limit;
        }*/
        /*for($i=0; $i<$num; $i++){
            if ($list[$i]['hidden'] == 0){
                $checked = 'checked';
            } else {
                $checked = '';
            }
            $str1 = "<a title='编辑' class='layui-btn layui-btn-sm' onclick='editPer(".json_encode($list[$i]).")'><i class='layui-icon'>&#xe642;</i></a><a title='删除' class='layui-btn layui-btn-sm' onclick='delPer(".$list[$i]['id'].")'>
<i class='layui-icon'>&#xe640;</i></a>";
            $str2 = "<image src='".$list[$i]['image']."' width='40px' height='40px'></image>";
            $str3 = '<input type="checkbox" value='.$list[$i]['hidden'].' data_id='.$list[$i]['id'].' lay-skin="switch" lay-filter="hideStatus" lay-text="已显示|已隐藏" '.$checked.'>';
            $str4 = '<input type="text" id="changeSort'.$list[$i]['id'].'" data-id='.$list[$i]['id'].' style="width:100%; height: 90%; padding-left:40%; padding-right:10%" onblur="changeSort('.$list[$i]['id'].')" name="sort" value="'.$list[$i]['sort'].'">';
            $list[$i]['operating'] = $str1;
            $list[$i]['image'] = $str2;
            $list[$i]['hidden'] = $str3;
            $list[$i]['sort'] = $str4;
        }*/
        $list = $query->from('prr_article_category', 'c')->where(array('uniacid' => $_W['uniacid'], 'is_del' => 0))->orderby($field, $order)->getall();
        foreach ($list as $key => $value) {
            if ($value['status'] == 0) {
                $list[$key]['title'] = $value['title'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='/addons/pruir/res/comment/icon/article.png' width='16px' height='16px'>";
            } else {
                $list[$key]['title'] = $value['title'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='/addons/pruir/res/comment/icon/video2.png' width='16px' height='16px'>";
            }
        }
        $webData['data'] = $list;
        $webData['code'] = 0;
        //$webData['count'] = $count;
        return json_encode($webData);
    }

    /**
     * 添加 / 编辑分类
     * @return false|string
     */
    public function doWebAddOrUpdateCategory()
    {
        global $_GPC, $_W;
        $type = $_GPC['type'];
        if (empty($_GPC['title'])) {
            $webData['code'] = 3001;
            $webData['errorMsg'] = "标题不能为空";
        } else if ($_GPC['status'] === null || $_GPC['status'] == '') {
            $webData['code'] = 3002;
            $webData['errorMsg'] = "请选择分类类型";
        } else {
            $data['title'] = $_GPC['title'];
            $data['intr'] = $_GPC['intr'];
            $data['sort'] = $_GPC['sort'];
            $data['hidden'] = $_GPC['hidden'];
            $data['url'] = $_GPC['url'];
            $data['pid'] = $_GPC['pid'];
            $data['status'] = $_GPC['status'];
            if ($type == 1) {
                // 新增
                // 插入分类数据
                $data['image'] = '/attachment/' . $_GPC['face'];
                $data['add_time'] = time();
                $data['uniacid'] = $_W['uniacid'];
                $request = pdo_insert($this->tb_article_category, $data);
                if ($request > 0) {
                    $webData['code'] = 200;
                    $webData['errorMsg'] = "添加成功";

                } else {
                    $webData['code'] = 201;
                    $webData['errorMsg'] = "添加失败";
                }
            } elseif ($type == 2) {
                if ($_GPC['catId']) {
                    // 更新
                    if ($_GPC['face']) {
                        $data['image'] = '/attachment/' . $_GPC['face'];
                    }
                    $request = pdo_update($this->tb_article_category, $data, array('id' => $_GPC['catId'], 'uniacid' => $_W['uniacid']));
                    if ($request > 0) {
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "修改成功";

                    } else {
                        $webData['code'] = 202;
                        $webData['errorMsg'] = "修改失败";
                    }
                } else {
                    $webData['code'] = 204;
                    $webData['errorMsg'] = "非法参数";
                }
            } else {
                $webData['code'] = 203;
                $webData['errorMsg'] = "非法参数";
            }
        }
        return json_encode($webData);
    }

    /**
     * 删除分类
     * @return false|string
     */
    public function doWebDelCategory()
    {
        global $_GPC, $_W;
        $where['uniacid'] = $_W['uniacid'];
        $where['id'] = $_GPC['id'];

        $map['pid'] = $_GPC['id'];
        $map['is_del'] = 0;
        $map['uniacid'] = $_W['uniacid'];
        $res = pdo_get($this->tb_article_category, $map);

        if (!empty($res)) {
            $webData['code'] = 203;
            $webData['errorMsg'] = "该分类下含有子分类, 请删除子分类后继续操作";
        } else {
            $data['is_del'] = 1;
            $request = pdo_update($this->tb_article_category, $data, $where);
            if ($request > 0) {
                $webData['code'] = 200;
                $webData['errorMsg'] = "删除成功";

            } else {
                $webData['code'] = 202;
                $webData['errorMsg'] = "删除失败";
            }
        }

        return json_encode($webData);
    }

    /**
     * 文章管理
     */
    /**
     * 文章列表页
     */
    public function doWebArticle()
    {
        global $_W, $_GPC;
        include $this->template('article');
    }

    /**
     * 获取文章列表
     * @return false|string
     */
    public function doWebGetArticleList()
    {
        global $_GPC, $_W;
        $sql = "SELECT a.id,a.title,a.sort,a.image_input,c.title as cTitle,a.hidden,p.title as pTitle,c.status,a.is_hot FROM `wx_prr_article` a LEFT JOIN `wx_prr_article_category` c ON `a`.`cid` = `c`.`id`  LEFT JOIN `wx_prr_article_periodical` p ON `a`.`pid` = `p`.`id`  WHERE `a`.`uniacid` = 19 AND `a`.`is_del` = 0 ";
        if (!empty($_GPC['order'])) {
            $order = $_GPC['order'];
        } else {
            $order = 'desc';
        }
        if (!empty($_GPC['field'])) {
            $field = $_GPC['field'];
        } else {
            $field = 'sort';
        }
        $sql1 = " ORDER BY `a`." . $field . " " . $order;
        if (!empty($_GPC['pid'])) {
            $pid = $_GPC['pid'];
            $sql2 = " AND `a`.`pid` = " . $pid;
            $sql = $sql . $sql2;
        }
        if (!empty($_GPC['cid'])) {
            $cid = $_GPC['cid'];
            $sql3 = " AND `a`.`cid` = " . $cid;
            $sql = $sql . $sql3;
        }
        if (!empty($_GPC['title'])) {
            $title = $_GPC['title'];
            $sql4 = " AND `a`.`title` LIKE '%" . $title . "%'";
            $sql = $sql . $sql4;

        }
        $sql = $sql . $sql1;
        $count = count(pdo_fetchall($sql));
        $page = ($_GPC['page'] - 1) * $_GPC['limit'];
        $limit = $_GPC['limit'];
        $sql5 = "LIMIT " . $page . "," . $limit;
        $sql = $sql . " " . $sql5;
        $list = pdo_fetchall($sql);
        $listLen = count($list);
        if ($limit > $listLen) {
            $num = $listLen;
        } else {
            $num = $limit;
        }
        for ($i = 0; $i < $num; $i++) {
            if ($list[$i]['is_hot'] == 1) {
                $list[$i]['title'] = "<img src='/addons/pruir/res/comment/icon/hot.png' width='16px' height='16px'>".$list[$i]['title'];
            }
            if ($list[$i]['status'] == 2) {
                $list[$i]['title'] = "<img src='/addons/pruir/res/comment/icon/video.png' width='16px' height='16px'>".$list[$i]['title'];
            }
            if ($list[$i]['hidden'] == 0) {
                $checked = 'checked';
            } else {
                $checked = '';
            }

            $str1 = "<a title='编辑' class='layui-btn layui-btn-sm editArt' onclick='editArt(" . json_encode($list[$i]['id']) . ")'><i class='layui-icon'>&#xe642;</i></a><a title='删除' class='layui-btn layui-btn-sm' onclick='delArt(" . json_encode($list[$i]['id']) . ")'>
<i class='layui-icon'>&#xe640;</i></a>";
            $str2 = "<image src='" . $list[$i]['image_input'] . "' width='40px' height='40px'></image>";
            $str3 = '<input type="checkbox" value=' . $list[$i]['hidden'] . ' data_id=' . $list[$i]['id'] . ' lay-skin="switch" lay-filter="hideStatus" lay-text="已显示|已隐藏" ' . $checked . '>';
            $str4 = '<a title="点击预览"  onclick="preview(' . $list[$i]['id'] . ')">' . $list[$i]['title'] . '</a>';
            $str5 = '<input type="text" id="changeSort' . $list[$i]['id'] . '" data-id=' . $list[$i]['id'] . ' style="width:100%; height: 90%; padding-left:40%; padding-right:10%" onblur="changeSort(' . $list[$i]['id'] . ')" name="sort" value="' . $list[$i]['sort'] . '">';
            $list[$i]['operating'] = $str1;
            $list[$i]['image_input'] = $str2;
            $list[$i]['hidden'] = $str3;
            $list[$i]['title'] = $str4;
            $list[$i]['sort'] = $str5;
        }
        $webData['data'] = $list;
        $webData['code'] = 0;
        $webData['count'] = $count;

        return json_encode($webData);
    }

    /**
     * 获取一篇文章info
     * @return false|string
     */
    public function doWebGetArticleOne()
    {
        global $_GPC, $_W;
        $type = $_GPC['type'];
        $where['id'] = $_GPC['aid'];
        $where['uniacid'] = $_W['uniacid'];
        if ($type == 2) {
            $query = load()->object('query');
            $reques = $query->from('prr_article', 'a')->leftjoin('prr_article_category', 'c')->on('a.cid', 'c.id')->select('a.id', 'a.title', 'c.title as cTitle', 'a.author', 'a.content', 'c.status as cStatus')->where($where)->get();
        } else {
            $query = load()->object('query');
            $reques = $query->from('prr_article', 'a')->leftjoin('prr_article_category', 'c')->on('a.cid', 'c.id')->select('a.id', 'a.title', 'a.cid', 'a.pid', 'a.author', 'a.image_input', 'a.video', 'a.synopsis', 'a.sort', 'a.content', 'a.url', 'a.add_time', 'a.hidden', 'a.admin_id', 'a.is_hot', 'a.is_del',  'a.image_banner', 'c.title as cTitle', 'c.status as cStatus')->where($where)->get();
        }
        if (!empty($reques)) {
            $webData['code'] = 200;
            $reques['content'] = htmlspecialchars_decode($reques['content']);
            $webData['info'] = $reques;
        } else {
            $webData['code'] = 201;
        }

        return json_encode($webData);
    }

    /**
     * 添加 / 编辑 文章
     */
    public function doWebAddOrUpdateArticle()
    {
        global $_GPC, $_W;
        $type = $_GPC['type'];
        $data['title'] = $_GPC['title'];
        $data['cid'] = $_GPC['cid'];
        $data['pid'] = $_GPC['pid'];
        $data['sort'] = $_GPC['sort'];
        $data['content'] = $_GPC['content'];
        $data['author'] = $_GPC['author'];
        $data['synopsis'] = $_GPC['synopsis'];
        $data['hidden'] = $_GPC['hide'];
        $data['is_hot'] = $_GPC['is_hot'];
        //$data['is_banner'] = $_GPC['is_banner'];
        $articleType = $_GPC['articleType'];
        if ($data['title'] === null || $data['title'] == '') {
            $webData['code'] = 3000;
            $webData['errorMsg'] = "标题不能为空";
        } else if ($data['cid'] === null || $data['cid'] == '') {
            $webData['code'] = 3001;
            $webData['errorMsg'] = "请选择分类";
        } else if ($data['pid'] === null || $data['pid'] == '') {
            $webData['code'] = 3002;
            $webData['errorMsg'] = "请选择期数";
        } else if ($data['author'] === null || $data['author'] == '') {
            $webData['code'] = 3006;
            $webData['errorMsg'] = "作者名不能为空";
        } else if (($data['content'] === null || $data['content'] == '') && $articleType == 1) {
            $webData['code'] = 3003;
            $webData['errorMsg'] = "请添加文章内容";
        } else if ((($_GPC['video'] === null || $_GPC['video'] == '') && $articleType == 2) && $type == 1) {
            $webData['code'] = 3004;
            $webData['errorMsg'] = "请上传视频";
        } else if (($_GPC['face'] === null || $_GPC['face'] == '') && $type == 1) {
            $webData['code'] = 3005;
            $webData['errorMsg'] = "请上传封面图";
        } /*else if ($data['is_banner'] == 1 && empty($_GPC['imgBanner'])) {
            $webData['code'] = 3006;
            $webData['errorMsg'] = "请上传banner图";
        } */else {
            if ($type == 1) {
                // 新增
                // 插入文章数据
                $data['image_input'] = '/attachment/' . $_GPC['face'];
                $data['image_banner'] = '/attachment/' . $_GPC['imgBanner'];
                $data['add_time'] = time();
                $data['admin_id'] = $_W['uid'];
                $data['uniacid'] = $_W['uniacid'];
                $request = pdo_insert($this->tb_article, $data);
                if ($request > 0) {
                    $webData['code'] = 200;
                    $webData['errorMsg'] = "添加成功";

                } else {
                    $webData['code'] = 201;
                    $webData['errorMsg'] = "添加失败";
                }
            } elseif ($type == 2) {
                if ($_GPC['aid']) {
                    // 更新
                    if ($_GPC['face']) {
                        $data['image_input'] = '/attachment/' . $_GPC['face'];
                    }
                    if ($_GPC['imgBanner']){
                        $data['image_banner'] = '/attachment/' . $_GPC['imgBanner'];
                    }
                    if ($_GPC['video']) {
                        $data['video'] = $_GPC['video'];
                        if ($_GPC['oldVideo'] !== null || $_GPC['oldVideo'] != '') {
                            unlink(dirname(dirname(__DIR__)) . $_GPC['oldVideo']);
                        }
                    }
                    $request = pdo_update($this->tb_article, $data, array('id' => $_GPC['aid'], 'uniacid' => $_W['uniacid']));
                    if ($request > 0) {
                        $webData['code'] = 200;
                        $webData['errorMsg'] = "修改成功";

                    } else {
                        $webData['code'] = 202;
                        $webData['errorMsg'] = "修改失败或没有变化";
                    }
                } else {
                    $webData['code'] = 204;
                    $webData['errorMsg'] = "非法参数";
                }
            } else {
                $webData['code'] = 203;
                $webData['errorMsg'] = "非法参数";
            }
        }
        return json_encode($webData);
    }

    /**
     * 删除文章
     * @return false|string
     */
    public function doWebDelArticle()
    {
        global $_GPC, $_W;
        $where['uniacid'] = $_W['uniacid'];
        $where['id'] = $_GPC['id'];

        $data['is_del'] = 1;
        $request = pdo_update($this->tb_article, $data, $where);
        if ($request > 0) {
            $webData['code'] = 200;
            $webData['errorMsg'] = "删除成功";

        } else {
            $webData['code'] = 202;
            $webData['errorMsg'] = "删除失败";
        }

        return json_encode($webData);
    }


    /**
     * 改变隐藏状态
     */
    public function doWebChangeHide()
    {
        global $_GPC, $_W;

        $type = $_GPC['type'];
        $where['id'] = $_GPC['id'];
        $where['uniacid'] = $_W['uniacid'];
        if ($type == 1) {
            $table = $this->tb_article;
        } else if ($type == 2) {
            $table = $this->tb_article_category;
        } else if ($type == 3) {
            $table = $this->tb_article_periodical;
        } elseif ($type == 4) {
            $table = $this->tb_banner;
        } else {
            $webData['code'] = 202;
            $webData['errorMsg'] = "errorCode: 202  errorMsg: The type parameter is incorrect!";
            return json_encode($webData);
        }

        $data['hidden'] = $_GPC['hide'];
        $request = pdo_update($table, $data, $where);
        if (!empty($request)) {
            $webData['code'] = 200;
        } else {
            $webData['code'] = 201;
        }

        return json_encode($webData);
    }

    /**
     * 上传视频
     * @return false|string
     */
    public function doWebUploadVideo()
    {
        global $_GPC, $_W;
        if ($_FILES['videos']['error'] > 0) {
            $webData['code'] = $_FILES['videos']['error'];
        } else {
            if (file_exists(dirname(dirname(__DIR__)) . "/attachment/videos/2/2019/upload/" . $_FILES["videos"]["name"])) {
                $webData['code'] = 3000;
                $webData['errMsg'] = "文件已存在";
            } else {
                // 文件名 生成规则
                $newName = time() . rand(10000, 99999) . $_FILES["videos"]["name"];
                move_uploaded_file($_FILES["videos"]["tmp_name"], dirname(dirname(__DIR__)) . "/attachment/videos/2/2019/upload/" . $newName);
                $webData['code'] = 200;
                $webData['src'] = "/attachment/videos/2/2019/upload/" . $newName;
            }
        }
        return json_encode($webData);
    }

    /**
     * 改变排序
     */
    public function doWebChangeSort()
    {
        global $_GPC, $_W;
        $where['id'] = $_GPC['id'];
        $where['uniacid'] = $_W['uniacid'];
        if ($_GPC['type'] == 1) {
            $table = $this->tb_article_periodical;
        } elseif ($_GPC['type'] == 2) {
            $table = $this->tb_article_category;
        } elseif ($_GPC['type'] == 3) {
            $table = $this->tb_article;
        } elseif ($_GPC['type'] == 4) {
            $table = $this->tb_banner;
        } else {
            $webData['code'] = 201;
            $webData['errorMsg'] = 'code: 201 errorMsg: 参数丢失';
            return json_encode($webData);
        }

        $data['sort'] = $_GPC['sort'];
        $request = pdo_update($table, $data, $where);
        if (!empty($request)) {
            $webData['code'] = 200;
            $webData['errorMsg'] = '修改成功!';
        } else {
            $webData['code'] = 202;
            $webData['errorMsg'] = '修改失败!';
        }

        return json_encode($webData);
    }

    /**
     * 获取分类树
     * @return false|string
     */
    public function doWebGetCategoryTree()
    {
        global $_GPC, $_W;
        $where['uniacid'] = $_W['uniacid'];
        $where['is_del'] = 0;
        $reques = pdo_getall($this->tb_article_category, $where, array('id', 'pid', 'title', 'hidden', 'status'));
        if (!empty($reques)) {
            $tree['list'] = $this->getTree($reques, 0, 0);
            $tree['code'] = 200;
        } else {
            $tree['code'] = 201;
        }
        return json_encode($tree);
    }

    /**
     * 获取期刊select
     */
    public function doWebGetPeriodicalTree()
    {
        global $_GPC, $_W;
        $where['uniacid'] = $_W['uniacid'];
        //$where['hidden'] = 0;
        $where['is_del'] = 0;
        $reques = pdo_getall($this->tb_article_periodical, $where, array('id', 'pid', 'title', 'hidden'));
        if (!empty($reques)) {
            $tree['list'] = $this->getTree($reques, 0, 0);
            $tree['code'] = 200;
        } else {
            $tree['code'] = 201;
        }
        return json_encode($tree);
    }

    public function getTree($array, $pid, $level)
    {
        $tree = [];
        $level++;
        foreach ($array as $key => $value) {
            if ($value["pid"] == $pid) {
                // 再找本类下的子类
                $value["title"] = str_repeat(" |-", $level) . $value["title"];
                $value['child'] = $this->getTree($array, $value["id"], $level);
                $tree[] = $value;
            }
        }
        return $tree;
    }
}