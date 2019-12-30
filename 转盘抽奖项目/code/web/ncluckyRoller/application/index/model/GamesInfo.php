<?php
namespace app\index\model;

use think\Db;
use think\Model;

class GamesInfo extends Model
{
    private $tableName = "weixin_gameslist";

    public function getGameInfo($where, $field)
    {
        $data = Db::name($this->tableName)
            ->field($field)
            ->where($where)
            ->find();
        return $data;
    }




}