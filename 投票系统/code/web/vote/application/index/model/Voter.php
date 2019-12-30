<?php
namespace app\index\model;

use think\Model;

class Voter extends Model{

    protected $name = "vote_active_voter";

    static function checkInfo($where){
        return self::where($where)->value('id');
    }

    static function insertIntoUserInfo($data){
        $obj =  self::create($data);
        return $obj->id;
    }

    static function updateIntoUserInfo($data, $where){
        return self::update($data, $where);
    }
}