<?php
defined('IN_IA') or exit('Access Denied');
class PruirModuleWxapp extends WeModuleWxapp
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
     * banner
     * @return false|string
     */
    public function doPageGetBanner()
    {
        global $_W, $_GPC;
        $where['uniacid'] = $_GPC['i'];
        $where['is_del'] = 0;
        $where['hidden'] = 0;

        $query = load()->object('query');
        //$data = $query->from('prr_article')->select('image_input','image_banner', 'id', 'pid', 'cid')->where($where)->orderby('id', 'desc')->limit(0,4)->getall();
        $data = $query->from('prr_banner')->select('id','title', 'image', 'sort', 'url')->where($where)->orderby(['sort'=>'desc', 'id'=>'desc'])->limit(0,1)->getall();
        if(!empty($data)){
            $webData['code'] = 200;
            $webData['data'] = $data;
        } else {
            $webData['code'] = 201;
        }
        return json_encode($webData);
    }

    /**
     * 获取文章
     * @return false|string
     */
    public function doPageGetArticlesList()
    {
        global $_W, $_GPC;
        $type = $_GPC['type'];
        $where['uniacid'] = $_GPC['i'];
        $where['is_del'] = 0;
        $where['hidden'] = 0;
        $query = load()->object('query');
        if($type == 1){
            // 获取最新一期id
            $pid = $query->from('prr_article_periodical')->select('id')->where($where)->orderby('id', 'desc')->page(0,1)->get();
            if (!empty($pid['id'])){
                $where['pid'] = $pid['id'];
            }
            $where['c.status'] = 0;
            $data = $query->from('prr_article', 'a')->leftjoin('prr_article_category', 'c')->on('a.cid', 'c.id')->select('a.id', 'a.title', 'a.image_input', 'c.title as cTitle', 'a.pid', 'a.cid', 'a.sort')->where($where)->orderby('a.sort', 'desc')->getall();
            $data = $this->checkData($data, $query);
            if(!empty($data)){
                $webData['code'] = 200;
                $webData['data'] = $data;
            } else {
                $webData['code'] = 201;
            }
        } elseif($type == 2) {
            $where['id'] = $_GPC['id'];
            $data = $query->from('prr_article', 'a')->leftjoin('prr_article_category', 'c')->on('a.cid', 'c.id')->select('a.id', 'a.title', 'a.author', 'c.title as cTitle', 'a.content', 'a.pid', 'a.cid', 'a.uniacid')->where($where)->limit(0,1)->getall();
            $data = $this->checkData($data, $query);
            if($data[0]['is_del'] == 1){
                $webData['code'] = 202;
                $webData['errorMsg'] = "code: 202  errorMsg: 无法查看该文章";
            } else if($data[0]["hidden"] == 1){
                $webData['code'] = 203;
                $webData['errorMsg'] = "code: 203  errorMsg: 无法查看该文章";
            } else if($data[0]["uniacid"] != $_GPC['i']){
                $webData['code'] = 204;
                $webData['errorMsg'] = "code: 204  errorMsg: 网络开小差了 请重试";
            } else {
                $webData['code'] = 200;
                //$data['content'] = htmlspecialchars_decode($data['content']);
                $webData['data'] = $data;
            }
        }  elseif($type == 3) {
            $where['pid'] = $_GPC['id'];
            $data = $query->from('prr_article', 'a')->leftjoin('prr_article_category', 'c')->on('a.cid', 'c.id')->select('a.id', 'a.title', 'a.image_input', 'c.title as cTitle', 'c.id as cid', 'a.pid','a.sort')->where($where)->getall();
            $data = $this->checkData($data, $query);
            $newData = array();
            if(!empty($data)){
                // 分类显示
                foreach ($data as $key =>$value){
                    $newData[$value['cTitle']][] = $value;
                }
                $webData['code'] = 200;
                $webData['list'] = $newData;
            } else {
                $webData['code'] = 206;
                $webData['errorMsg'] = "code: 206 errorMsg: 暂无数据";
            }
        } else {
            $webData['code'] = 205;
            $webData['errorMsg'] = "code: 205  errorMsg: 网络开小差了 请重试";
        }

        return json_encode($webData);
    }

    /**
     * 企业培训
     */
    public function doPageCorporateTraining()
    {
        global $_W, $_GPC;
        $where['pid'] = 2;
        $where['uniacid'] = $_GPC['i'];
        $where['is_del'] = 0;
        $where['hidden'] = 0;
        $data = pdo_getall($this->tb_article_category, $where);
        if(!empty($data)){
            $webData['code'] = 200;
            $webData['data'] = $data;
        } else {
            $webData['code'] = 201;
        }
        return json_encode($webData);

    }

    public function doPageGetVideoList()
    {
        global $_W, $_GPC;
        $where['cid'] = $_GPC['id'];

        $data = pdo_getall($this->tb_article, $where);
        if(!empty($data)){
            $webData['code'] = 200;
            $webData['list'] = $data;
        } else {
            $webData['code'] = 201;
        }

        return json_encode($webData);
    }

    /**
     * 获取期刊
     * @return false|string
     */
    public function doPageGetPeriodicalList()
    {
        global $_W, $_GPC;
        $where['uniacid'] = $_GPC['i'];
        $where['is_del'] = 0;
        $where['hidden'] = 0;

        $query = load()->object('query');
        $data = $query->from('prr_article_periodical')->select('id', 'title', 'image')->where($where)->orderby('id', 'desc')->getall();
        if(!empty($data)){
            $webData['code'] = 200;
            $webData['data'] = $data;
        } else {
            $webData['code'] = 201;
        }

        return json_encode($webData);
    }

    /**
     * 验证数据(暂用)
     * @param $data
     * @param $query
     * @return mixed
     */
    public function checkData($data, $query)
    {
        // 屏蔽被隐藏的类和分期下的文章
        foreach($data as $key => $value){
            $res1 = $query->from('prr_article_category')->select('hidden')->where(array('id'=> $value['cid']))->get();
            if($res1['hidden'] == 1){
                unset($data[$key]);
                continue;
            }
            $res2 = $query->from('prr_article_periodical')->select('hidden')->where(array('id'=> $value['pid']))->get();
            if ($res2['hidden'] == 1 ){
                unset($data[$key]);
                continue;
            }
        }
        return  $data;
    }



}