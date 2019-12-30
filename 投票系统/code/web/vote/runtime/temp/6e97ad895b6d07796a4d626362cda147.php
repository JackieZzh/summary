<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"/data/www/weixin.prykweb.com/weixintest/vote/public/../application/index/view/index/index.html";i:1563526861;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $data['title']; ?></title>
        <meta name="keywords" content="普瑞眼科">
        <meta name="description" content="普瑞眼科">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <link rel="stylesheet" type="text/css" href="../static/css/style.css" />
        <script type="text/javascript" src="../static/js/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="../static/js/vote.js"></script>

        <style>
            .model dl dd:nth-of-type(2){ overflow: hidden; height:2rem;line-height: 130%;}
        </style>
    </head>
    
    <body style="background-color:<?php echo $data['background_color']; ?>">
    <input type="hidden" id="code" value="<?php echo $data['code']; ?>">
    <input type="hidden" id="aid" value="<?php echo $data['id']; ?>">
    <?php switch($data['code']): case "200": ?>
        <div class="contrainer">
        <input type="hidden" id="bgc" value="<?php echo $data['background_color']; ?>">
        <input type="hidden" id="bgc_rote" value="<?php echo $data['background_color_rote']; ?>">
        <input type="hidden" id="voter_id" value="<?php echo $data['voter']['id']; ?>">
        <input type="hidden" id="voter_openid" value="<?php echo $data['voter']['openid']; ?>">
        <input type="hidden" id="time" value="<?php echo $data['time']; ?>">
        <div class="top">
            <img src="/attachment/<?php echo $data['background_img']; ?>" width="100%" />
        </div>

        <div class="top_menu">
            <p class="top_menu1" onclick="ShowDiv('MyDiv','fade')">奖品</p>
            <p class="top_menu2" onclick="ShowDiv('MyDiv1','fade')">规则</p>
        </div>

        <div id="fade" class="black_overlay"></div>
        <div id="MyDiv" class="white_content">
            <div class="close_icon">
                <span onclick="CloseDiv('MyDiv','fade')"><img src="../static/img/close.png" width="100%"/></span>
            </div>
            <h1><img src="/attachment/<?php echo $data['goods_url']; ?>" width="100%"/></h1>
        </div>

        <div id="fade" class="black_overlay"></div>
        <div id="MyDiv1" class="white_content">
            <div class="close_icon">
                <span onclick="CloseDiv('MyDiv1','fade')"><img src="../static/img/close.png" width="100%"/></span>
            </div>
            <h1><img src="/attachment/<?php echo $data['rote_url']; ?>" width="100%"/></h1>
        </div>

        <!--倒计时-->
        <div class="time-item">
            <div class="time_img"><img src="/attachment/<?php echo $data['timer_img']; ?>" width="100%" /></div>
            <div class="time_title"></div>
            <div class="content">
                <input type="text" disabled="disabled" id="time_d">天
                <input type="text" disabled="disabled" id="time_h">时
                <input type="text" disabled="disabled" id="time_m">分
                <input type="text" disabled="disabled" id="time_s">秒
            </div>
        </div>

        <!--选手-->
        <div class="model">
            <?php if(is_array($data['part']) || $data['part'] instanceof \think\Collection || $data['part'] instanceof \think\Paginator): $index = 0; $__LIST__ = $data['part'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($index % 2 );++$index;?>
            <dl>
                <dt><img src="/attachment/<?php echo $item['avatar_url']; ?>" width="100%" /></dt>
                <?php if($item['nick_name']): ?>
                <dd><?php echo $item["nick_name"]; ?></dd>
                <?php else: ?>
                <dd><?php echo $item["real_name"]; ?></dd>
                <?php endif; ?>
                <dd><?php echo $item["introduction"]; ?></dd>
                <dd><?php echo $index; ?></dd>
                <div class="vote">
                    <div class="vote1">
                        <p>票数：<a id="voter<?php echo $item['id']; ?>" ><?php echo $item['votes']; ?></a></p>
                    </div>
                    <div class="vote2">
                        <p><a class="tou1"  data-id="<?php echo $item['id']; ?>">投票</a></p>
                    </div>
                </div>
            </dl>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>


    </div>
        <?php break; case "2001": ?>
            <div class="contrainer">
                <input type="hidden" id="time" value="<?php echo $data['time']; ?>">
                <div class="top">
                    <img src="/attachment/<?php echo $data['background_img']; ?>" width="100%" />
                </div>
                <div class="top_menu">
                    <p class="top_menu1" onclick="ShowDiv('MyDiv','fade')">奖品</p>
                    <p class="top_menu2" onclick="ShowDiv('MyDiv1','fade')">规则</p>
                </div>
                <div id="fade" class="black_overlay"></div>
                <div id="MyDiv" class="white_content">
                    <div class="close_icon">
                        <span onclick="CloseDiv('MyDiv','fade')"><img src="../static/img/close.png" width="100%"/></span>
                    </div>
                    <h1><img src="/attachment/<?php echo $data['goods_url']; ?>" width="100%"/></h1>
                </div>

                <div id="fade" class="black_overlay"></div>
                <div id="MyDiv1" class="white_content">
                    <div class="close_icon">
                        <span onclick="CloseDiv('MyDiv1','fade')"><img src="../static/img/close.png" width="100%"/></span>
                    </div>
                    <h1><img src="/attachment/<?php echo $data['rote_url']; ?>" width="100%"/></h1>
                </div>
                <!--倒计时-->
                <div class="time-item">
                    <div class="time_img"><img src="/attachment/<?php echo $data['timer_img']; ?>" width="100%" /></div>
                    <div class="time_title"></div>
                    <div class="content">
                        <input type="text" disabled="disabled" id="time_d">天
                        <input type="text" disabled="disabled" id="time_h">时
                        <input type="text" disabled="disabled" id="time_m">分
                        <input type="text" disabled="disabled" id="time_s">秒
                    </div>
                </div>
            </div>
        <?php break; case "2002": ?>
            <div class="contrainer">
                <div class="top">
                    <img src="/attachment/<?php echo $data['background_img']; ?>" width="100%" />
                </div>
                <div class="top_menu">
                    <p class="top_menu1" onclick="ShowDiv('MyDiv','fade')">奖品</p>
                    <p class="top_menu2" onclick="ShowDiv('MyDiv1','fade')">规则</p>
                </div>
                <div id="fade" class="black_overlay"></div>
                <div id="MyDiv" class="white_content">
                    <div class="close_icon">
                        <span onclick="CloseDiv('MyDiv','fade')"><img src="../static/img/close.png" width="100%"/></span>
                    </div>
                    <h1><img src="/attachment/<?php echo $data['goods_url']; ?>" width="100%"/></h1>
                </div>

                <div id="fade" class="black_overlay"></div>
                <div id="MyDiv1" class="white_content">
                    <div class="close_icon">
                        <span onclick="CloseDiv('MyDiv1','fade')"><img src="../static/img/close.png" width="100%"/></span>
                    </div>
                    <h1><img src="/attachment/<?php echo $data['rote_url']; ?>" width="100%"/></h1>
                </div>
                <div class="time-item">
                    <div class="time_img"><img src="/attachment/<?php echo $data['timer_img']; ?>" width="100%" /></div>
                    <div class="time_title" id="time_title_2002">该活动已结束</div>
                    <div class="content" id="content_2002">
                        排行榜
                    </div>
                </div>
                <div class="model" id="mode1_2002"></div>
            </div>
        <?php break; case "2003": ?>
            <div class="contrainer">
                <div class="top"></div>
                <div class="time-item">
                    <div class="time_title"><?php echo $data['errorMsg']; ?></div>
                </div>
            </div>
        <?php break; case "2004": ?>
        <div class="contrainer">
            <div class="top">
            </div>
            <div class="time-item">
                <div class="time_title"><?php echo $data['errorMsg']; ?></div>
            </div>
        </div>
        <?php break; endswitch; ?>
    </body>

    <script>
        $(function(){
            var code = $('#code').val(),
                time = $('#time').val(),
                aid =  $("#aid").val();
            if (code == 200){
                $('.time_title').html("距离活动结束还有");
                show_time(time);

            }else if(code == 2001){
                $('.time_title').html("距离活动开始还有");
                show_time(time);
            } else if(code == 2002){
                $.ajax({
                    url: "https://weixin.prykweb.com/weixintest/vote/public/getLeaderBoard",
                    data:{
                      id: aid
                    },
                    success: function (res) {
                        res = JSON.parse(res);
                        var text = "";
                        if(res.code == 200){
                            $.each(res.data, function (i, n) {
                                var c = parseInt(i) + 1;
                                text += '<dl >';
                                text += '<dt >';
                                text += '<img src="/attachment/' + n.avatar_url + '" width="100%">';
                                text += '</dt >';
                                text += '<dd >' + n.name + '</dd>';
                                text += '<dd >' + c + '</dd>';
                                text += '<div  class="vote">';
                                text += '<div  class="vote1">';
                                text += '<p> 票数：';
                                text += '<a class="num">' + n.votes + '</a>';
                                text += '</p >';
                                text += '</div  >';
                                text += '</div>';
                                text += ' </dl>';
                                $(".model").html(text);
                            })
                        } else {

                        }

                    },
                    error: function (xhr, type, errorThrown) {
                        //异常处理；
                        console.log('访问错误,代码' + xhr.status);
                    }
                })
            }
            $('.model').css('background-color', $('#bgc').val());
            $('.top_menu p').css('background-color', $('#bgc_rote').val())

        });

        $('.tou1').on('click', function(){
            var id = $(this).attr('data-id'),
                vid = $('#voter_id').val(),
                openid = $('#voter_openid').val(),
                aid = $('#aid').val(),
                a = $('#voter'+id).html();

            if(openid == null || openid ==""){
                window.location.reload()
            } else {
                $.ajax({
                    url: "https://weixin.prykweb.com/weixintest/vote/public/doVote",
                    data:{
                        pid : id,
                        vid: vid,
                        openid: openid,
                        aid: aid
                    },
                    success: function(res){
                        res = JSON.parse(res);
                        if (res.code == 200){
                            var b = a * 1 + 1;
                            $('#voter'+id).html(b);
                            alert(res.errorMsg)
                        } else {
                            alert(res.errorMsg)
                        }
                    }
                })
            }

        });

        function show_time(time){
            var u_time = time;
            // 剩余天数
            var d = parseInt(u_time/86400);
            u_time -= d * 86400;
            var h = parseInt(u_time/3600);

            u_time -= h * 3600;
            var i = parseInt(u_time/60);

            u_time -= i * 60;
            var s = u_time;
            // 时分秒为单数时、前面加零
            if(d < 10){
                d = "0" + d;
            }
            if(h < 10){
                h = "0" + h;
            }
            if(i < 10){
                i = "0" + i;
            }
            if(s < 10){
                s = "0" + s;
            }
            // 显示时间
            $("#time_d").val(d);
            $("#time_h").val(h);
            $("#time_m").val(i);
            $("#time_s").val(s);
            // 设置定时器
            setTimeout(function(){
                 if (time > 1){
                     time -= 1;
                     show_time(time)
                 } else {
                     window.location.href = "https://weixin.prykweb.com/weixintest/vote/public/vote/"+ $('#aid').val();
                 }
            },1000);
        }
    </script>
</html>
