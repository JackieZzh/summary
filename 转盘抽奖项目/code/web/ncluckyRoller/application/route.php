<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

route::get("intoHtml", "index/gamestest/intoHtml");

route::get('beforeTheLuckyRoller/:gameId', 'index/games/beforeTheLuckyRoller');
//route::post('beforeTheLuckyRoller', 'index/games/beforeTheLuckyRoller');

route::get('wxTest/:gameId',"index/games/afterGetWxCode");

route::post("getRes", "index/games/getTheLuckyRollerResult");

route::get("phpInfo", "index/index/phpInfo");

route::post("appLetGetInfo", "index/weChat/onlogin");

route::get("test", "index/test/test");

route::post("commitInfo", "index/games/userCommitInfo");

route::get("beginGames/:gid", "admin/gameadmin/startLuckyGames");

route::get("reset/:gid", "admin/gameadmin/resetRedis");

route::get("showRedis/:gid", "admin/gameadmin/showRedis");

route::post("errorCommit", "index/games/errorCommitUserInfo");



// 南昌小程序
route::post("appletLuckyGetGoods", "index/weChat/appletLuckyGetGoods");

route::post("appletUserInfo", "index/weChat/appletAddOrUpdateUserInfo");

route::post("appletGameStatus", "index/weChat/appletJudgeLuckyGameStatus");

route::post("ncSubmitInfo", "index/weChat/submitNcUserInfo");

// 抽奖活动临时后台
route::get("temporaryAdmin/:gameId", "admin/gameadmin/adminIndex");


route::get("voteActive/:id", "index/voteActive/index");

