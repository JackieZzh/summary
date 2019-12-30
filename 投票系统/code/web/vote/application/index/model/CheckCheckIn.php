<?php
namespace app\index\model;

use think\Model;

class CheckCheckIn extends Model
{
    protected $name = "bjhd_active_check_in";

    public static function checkCheckIn($where, $field)
    {
        return self::field($field)->where($where)->find();
    }

    public static function doCheckIn($data)
    {
        return self::create($data)->id;
    }
}