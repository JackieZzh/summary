<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:103:"/data/www/weixin.prykweb.com/weixintest/theLuckyRoller/public/../application/admin/view/game/index.html";i:1548753805;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $data['title']; ?>抽奖游戏临时后台</title>
    <style>
        .userInfo{
            margin-bottom:10px;
        }
    </style>

</head>
<body>
    <div class="gameStatus">
        <span>游戏状态:<?php echo $data['gameStatus']['status']; ?></span> <br>
        <span>开始时间:<?php echo $data['gameStatus']['createtime']; ?></span> <br>
        <span>结束时间:<?php echo $data['gameStatus']['expirestime']; ?></span> <br>
    </div>

    <div class="userList">
        <?php if(is_array($data['userGoodsList']) || $data['userGoodsList'] instanceof \think\Collection || $data['userGoodsList'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data['userGoodsList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <div class="userInfo">用户<?php echo $vo['realname']; ?> 电话号码:<?php echo $vo['tel']; ?> 于 <?php echo $vo['time']; ?> 获取 <?php echo $vo['title']; ?></div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</body>
</html>