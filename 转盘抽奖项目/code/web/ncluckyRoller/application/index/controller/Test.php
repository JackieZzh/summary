<?php
namespace app\index\controller;

use think\Controller;
use think\cache\driver\Redis;

class Test extends Controller
{
    private $redis ;

    public function __construct()
    {
        $redis = new Redis();
        $this->redis = $redis->handler();
    }

    public function test()
    {
        /*$path = "/data/www/weixin.prykweb.com/weixintest/theLuckyRoller/public/wx/redis";
        $list = glob($path.'*.php');
        if( !empty($list[0]) ){
            unlink($list[0]);
        }*/
    }


}