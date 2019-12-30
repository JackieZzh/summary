<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Goods;
use app\admin\model\GamesInfo;
use think\cache\driver\Redis;

// expire

class Gameadmin extends Controller
{
    private $redis;
    private $goods;
    private $games;

    public function __construct()
    {
        $redis = new Redis;
        $this->redis = $redis->handler();
        $this->goods = new Goods;
        $this->games = new GamesInfo;
    }

    public function adminIndex($gameId)
    {
        if($gameId == 1){
            $webData["title"] = "上海";
        } elseif ($gameId == 2){
            $webData["title"] = "昆明";
        } elseif ($gameId == 3){
            $webData["title"] = "重庆";
        } elseif ($gameId == 4){
            $webData["title"] = "哈尔滨";
        } elseif ($gameId == 5){
            $webData["title"] = "成都";
        }
        $webData["gameId"] = $gameId;

        // 游戏状态
        $res = $this->games->getGameInfo(array("id"=>$gameId), "createtime, expirestime, status");
        $res["createtime"] = date("Y-m-d ", $res["createtime"]);
        $res["expirestime"] = date("Y-m-d ", $res["expirestime"]);

        // 获奖列表
        $goodsList = $this->games->getSomeGiftList($gameId);
        foreach ($goodsList as $key => $value){
            $goodsList[$key]["time"] = date("Y-m-d H:i:s", $value["time"]);
        }


        $webData["gameStatus"] = $res;
        $webData["userGoodsList"] = $goodsList;
        return view("game/index",[
            "data" => $webData
        ]);
    }

    /**
     * @param $gid
     * 开启抽奖
     */
    public function startLuckyGames($gid)
    {
        $goodsIds = $this->goods->getGamesGoodsList(array("gid"=> $gid), "id, num");
        $expTime = $this->games->getGameInfo(array("id"=> $gid), "expirestime");
        $redisExpTime = $expTime["expirestime"] - time();
        // 设置goods hash 防并发
        foreach ($goodsIds as $key => $value ){
            $this->redis->hSet("luckyGame".$gid, $value["id"], $value["num"]);
            $this->redis->expire("luckyGame".$gid, $redisExpTime);
        }
    }

    /**
     * @param $gid
     * 重置抽奖
     */
    /*public function resetRedis($gid)
    {
        $this->redis->del("luckyGame".$gid);
        $this->startLuckyGames($gid);
    }*/

    /**
     * @param $gid
     * @return string
     * 查看剩余个数
     */
    public function showRedis($gid)
    {
        $goodsIds = $this->goods->getGamesGoodsList(array("gid"=> $gid), "id, num");
        $arr = array();
        foreach( $goodsIds as $key => $value ){
            $res["id"] = $value["id"];
            $res["num"] = $this->redis->hGet("luckyGame".$gid, $value["id"]);
            array_push($arr,$res);
        }

        return json_encode($arr);
    }


}