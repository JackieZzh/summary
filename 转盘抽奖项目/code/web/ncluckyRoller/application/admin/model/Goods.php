<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Goods extends Model
{
    public function getGamesGoodsList($where,$field)
    {
        $res = Db::name("weixin_gamesgoodslist")
            ->field($field)
            ->where($where)
            ->select();

        return $res;
    }

    public function insertIntoUserGoodsList($data)
    {
        $res = Db::name("weixin_goodslistforgame")
            ->insert($data);

        return $res;
    }



}