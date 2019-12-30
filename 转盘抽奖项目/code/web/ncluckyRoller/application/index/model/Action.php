<?php
namespace app\index\model;

use think\Db;
use think\Model;

class Action extends Model
{
    private $tableName = "weixin_useraction";

    public function addAction($data)
    {
        Db::name($this->tableName)
            ->insert($data);
    }
}