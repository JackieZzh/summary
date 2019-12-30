<?php
namespace app\index\model;

use think\Model;

class CheckActive extends Model
{
    protected $name = "bjhd_active";

    static function getActiveInfo($field, $where){
        $res = self::field($field)->where($where)->find();
        if (!empty($res)){
            $res = $res->toArray();
        }
        return $res;
    }
}