<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class GamesInfo extends Model
{
    private $tableName = "weixin_gameslist";

    public function getGameInfo($where,$field)
    {
        $data = Db::name($this->tableName)
            ->field($field)
            ->where($where)
            ->find();
        return $data;
    }

    public function getSomeGiftList($gameId)
    {
        $sql = "select l.`goodsid` , l.`time`, g.`title`, u.`realname`, u.`headimgurl`, u.`tel` from  (`wx_weixin_goodslistforgame` as l LEFT JOIN `wx_weixin_gamesgoodslist` as g ON  l.`goodsid` = g.`id`)  left join `wx_weixin_userinfo` as u  on l.`uid` = u.`id`   where l.`gid` =".$gameId;
        $res = Db::query($sql);
        return $res;
    }


}