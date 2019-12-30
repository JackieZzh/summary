<?php
namespace app\index\model;

use think\Model;

class Action extends Model
{
    protected $name = "vote_active_action";

    static function checkRemainder($where){
        return collection(self::field("count(`id`) as `num` ")
            ->where($where)
            ->whereTime('time', 'today')
            ->select())->toArray();
    }
    
    static function doVote($data){
        $obj =  self::create($data);
        return $obj->id;
    }
}