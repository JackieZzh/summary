<?php
namespace app\index\model;

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

    public function getSomeOneGiftList($uid, $gid)
    {
        $sql = "select l.`goodsid` , l.`time`, g.`title` from  `wx_weixin_goodslistforgame` as l LEFT JOIN `wx_weixin_gamesgoodslist` as g ON  l.`goodsid` = g.`id`  where l.`uid` = ".$uid." and l.`gid` =".$gid;
        $res = Db::query($sql);
        return $res;
    }

    public function getSomeGiftList($gameId)
    {
        $sql = "select l.`goodsid` , l.`time`, g.`title`, u.`nickname` from  (`wx_weixin_goodslistforgame` as l LEFT JOIN `wx_weixin_gamesgoodslist` as g ON  l.`goodsid` = g.`id`)  left join `wx_weixin_userinfo` as u  on l.`uid` = u.`id`   where l.`gid` =".$gameId." and u.`tel` != 'null' order by l.`time` desc LIMIT 10";
        $res = Db::query($sql);
        return $res;
    }

    public function getGoodStuff($gameId)
    {
        $sql = "select l.`goodsid` , l.`time`, g.`title`, u.`nickname` from  (`wx_weixin_goodslistforgame` as l LEFT JOIN `wx_weixin_gamesgoodslist` as g ON  l.`goodsid` = g.`id`)  left join `wx_weixin_userinfo` as u  on l.`uid` = u.`id`   where l.`gid` =".$gameId." and l.`goodsid` in (106, 114, 110, 115)  and u.`tel` != 'null' order by l.`time` desc LIMIT 10";
        $res = Db::query($sql);
        return $res;
    }

}