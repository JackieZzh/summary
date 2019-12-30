<?php
namespace app\index\model;

use think\Model;

class CheckParticipant extends Model
{
    protected $name = "bjhd_active_participant";

    static function checkInfo($where, $field){
        return self::field($field)->where($where)->find();
    }

    static function insertIntoUserInfo($data){
        $obj =  self::create($data);
        return $obj->id;
    }

    static function updateIntoUserInfo($data, $where){
        return self::update($data, $where);
    }
}