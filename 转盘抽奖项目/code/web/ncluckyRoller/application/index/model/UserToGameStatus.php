<?php
namespace app\index\model;

use think\Db;
use think\Model;

class UserToGameStatus extends Model
{
    private $tableName = "weixin_usertogamestatus";

    public function userTimes($where)
    {
        $data = Db::name($this->tableName)
            ->field("status, times")
            ->where($where)
            ->find();

        return $data;
    }

    public function initUserGameInfo($data)
    {
        Db::name($this->tableName)->insert($data);
    }

    public function updateGameTimes($where, $data)
    {
        $res = Db::name($this->tableName)
            ->where($where)
            ->update($data);
        return $res;
    }


}