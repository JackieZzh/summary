<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Users extends Model
{
    private $tableName = "weixin_userinfo";

    public function getOneUserInfo($where, $field)
    {
        $data = Db::name($this->tableName)
            ->field($field)
            ->where($where)
            ->find();

        return $data;
    }

    public function insertIntoUserInfo($data)
    {
        Db::name($this->tableName)->insert($data);
        $res = $this->getLastInsID();
        return $res;
    }

    public function updateIntoUserInfo($data, $where)
    {
        $webData = array();
        Db::startTrans();
        try{
            $res = Db::name($this->tableName)->where($where)->update($data);
            //var_dump(Db::name($this->tableName)->getLastSql());die;
            if (!$res){
                throw new \Exception("更新失败");
            }

            $webData["code"] = 60000;
            // 提交事物
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            $webData["code"] = 60001;
        }
        return $webData;
    }

}