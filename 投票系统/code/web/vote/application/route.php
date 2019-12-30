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

// 投票
route::get('auth/:id', 'index/index/beforeTheVote');

route::get('vote/:id', 'index/index/index');

route::get('doVote', 'index/index/doVote');

route::get('getLeaderBoard', 'index/index/leaderBoard');

route::get('poster/:aid', 'index/poster/index');






// 签到
route::get('checkIn/:id', 'index/check/index');

// 签到
route::post("doCheck", "index/check/doCheck");



Route::miss('index/miss/miss');