<?php
namespace app\index\controller;

use think\Controller;

class Poster extends Controller
{
    public function index($aid)
    {
        // todo 通过aid去查数据
        $top = "/data/www/weixin.prykweb.com/addons/applet_cs/res/test/images/top.jpg";
        $qrcode = "/data/www/weixin.prykweb.com/addons/applet_cs/res/test/images/test.png";
        $logo = "/data/www/weixin.prykweb.com/addons/applet_cs/res/test/images/logo.jpg";
        $data = [
            "title" => "光明卫士 点亮梦想｜普瑞眼科医疗队走进甘孜州",
            "hospital" => "成都普瑞眼科医院",
            "des" => "为积极响应国家“医疗下乡、精 准扶贫”的号召以及习总书记关于“共同呵护好孩子的眼睛，让他们拥有一个光明的未来”的重要指示，7月7日，在成都普瑞眼科医院的整体部署和安排下，“组团式”援藏医疗队来到四川甘孜州泸定县田坝八一爱民小学，开展青少近视防控眼健康公益普查。普瑞眼科用过硬的技术、执着的信念、真诚的关怀，赢得了当地藏族同胞的赞誉。",
            "topImg" => $this->base64EncodeImage($top),
            "qrcode" => $this->base64EncodeImage($qrcode),
            "logo" => $this->base64EncodeImage($logo)
        ];

        return view("poster/index",[
            'data' => $data
        ]);
    }

    /**
     * 图片转码base64
     * @param $image_file
     * @return string
     */
    public function base64EncodeImage ($image_file) {
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }

}