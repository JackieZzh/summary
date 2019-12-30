<?php
namespace app\index\model;

use think\Db;
use think\Model;

class UserInfo extends Model
{
    private $tableName = "xcx_nc_hjsg_booklist";

    public function findUser($where)
    {
        $res = Db::name($this->tableName)
            ->where($where)
            ->find();
        return $res;
    }

    public function updateUserInfo($data, $where)
    {
        $res = Db::name($this->tableName)
            ->where($where)
            ->update($data);

        return $res;
    }

    public function insertIntoUserInfo($data)
    {
        $res = Db::name($this->tableName)
            ->insert($data);

        return $res;
    }
}