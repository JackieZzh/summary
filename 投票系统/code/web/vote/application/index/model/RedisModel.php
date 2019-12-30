<?php
namespace app\index\model;

use think\Model;
use think\cache\driver\Redis;

class RedisModel extends Model
{
    private $redis;
    
    public function __construct()
    {
        $handle = new Redis();
        $this->redis = $handle->handler();
    }

    public function checkAndSet($aid, $openId, $time)
    {
        $cha = strtotime(date('Y-m-d 23:59:59')) - time();
        if (!$this->redis->exists($aid."|vote|".$openId)){
            $this->redis->setex($aid."|vote|".$openId, $cha, 0);
        }
        $res = $this->redis->incrby($aid."|vote|".$openId, 1);
        if ($res > $time){
            return false;
        } else{
            return true;
        }
    }
}