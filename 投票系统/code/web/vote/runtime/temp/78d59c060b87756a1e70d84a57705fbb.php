<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"/data/www/weixin.prykweb.com/weixintest/vote/public/../application/index/view/check/index.html";i:1571823203;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script type="text/javascript" src="../static/js/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../static/layui/css/layui.css" />

    <title><?php echo $webData['active']['title']; ?></title>
</head>
<body>
    <?php if($webData['code'] == 200): ?>
    <input type="hidden" id="a_id" value="<?php echo $webData['active']['id']; ?>">
    <input type="hidden" id="id" value="<?php echo $webData['voter']['id']; ?>">
    <input type="hidden" id="status" value="<?php echo $webData['voter']['status']; ?>">
    <input type="hidden" id="check" value="<?php echo $webData['check']; ?>">
    <?php if($webData['check'] == 1): ?>
    <input type="hidden" id="check_time" value="<?php echo $webData['check_time']; ?>">
    <?php else: ?>
        <div class="errorMsg"></div>
    <?php endif; else: ?>
    <div><?php echo $webData['errorMsg']; ?></div>
    <?php endif; ?>

    <!--<div id="box" style="display: none;">
        <form action="input"></form>
    </div>-->
</body>
<script type="text/javascript" src="../static/layui/layui.all.js"></script>
<script>
    $(function(){
        var check = $('#check').val();

        function check_in(){
            var s = $('#status').val(),
                id = $('#id').val(),
                a_id = $('#a_id').val();

            if(s == 2){
                layer.prompt({
                    title: '请输入真实姓名',
                    maxlength: 10,
                    btn: ['签到', '取消']
                },function(value, index, elem){
                    if(value){
                        $.ajax({
                            type: "post",
                            url: "<?php echo url('/doCheck'); ?>",
                            data:{
                                name: value,
                                id: id,
                                a_id: a_id
                            },
                            success: function(res){
                                res = JSON.parse(res);
                                if (res.code == 200){
                                    layer.close(index);
                                    layer.msg('签到成功! 请找现场工作人员领取奖品!', {icon:1, time:0}/*, function(){
                                        /!*$('#status').val(1);
                                        $('#check_btn').remove();*!/
                                    }*/)
                                } else {
                                    layer.msg(res.errorMsg, {icon:2, time: 2000})
                                }
                            }
                        });
                    } else {
                        return false
                    }


                });
            } else {
                $.ajax({
                    type: "post",
                    url: "<?php echo url('/doCheck'); ?>",
                    data:{
                        id: id,
                        a_id: a_id
                    },
                    success: function(res){
                        res = JSON.parse(res);
                        if (res.code == 200){
                            layer.msg('签到成功! 请找现场工作人员领取奖品!', {icon:1, time:0}/*, function(){
                                $('#status').val(1);
                                $('#check_btn').remove();
                                $('.errorMsg').html('您已签到!');
                            }*/)
                        } else {
                            layer.msg(res.errorMsg, {icon:2, time: 2000})
                        }
                    }
                });
            }
        }

        if (check == 2){
            check_in()
        } else {
            layer.msg('您已于 '+$('#check_time').val()+' 签到成功!', {icon:1, time:0})
        }
    });
</script>
</html>