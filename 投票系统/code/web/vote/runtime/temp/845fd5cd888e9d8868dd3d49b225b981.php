<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:95:"/data/www/weixin.prykweb.com/weixintest/vote/public/../application/index/view/poster/index.html";i:1563520860;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8" />
    <title>分享图片|普瑞眼科</title>
    <meta name="keywords" content="分享图片|普瑞眼科">
    <meta name="description" content="分享图片|普瑞眼科">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="../static/css/stylePoster.css" />
    <script type="text/javascript" src="../static/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="../static/js/html2canvas.js"></script>
    <script type="text/javascript" src="../static/js/canvas2image.js"></script>
    <script type="text/javascript">
        (function(doc, win) {
            var docEl = doc.documentElement,
                resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
                recalc = function() {
                    var clientWidth = docEl.clientWidth;
                    if (!clientWidth) return;
                    if (clientWidth >= 750) {
                        docEl.style.fontSize = '100px';
                    } else {
                        docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
                    }
                };

            if (!doc.addEventListener) return;
            recalc();
            win.addEventListener(resizeEvt, recalc, false);
            doc.addEventListener('DOMContentLoaded', recalc, false);
        })(document, window);
    </script>

</head>
<body>
<div style="position: relative" id="content">
    <div class="contrainer" id="contrainer">
    <div class="top">
        <img src="<?php echo $data['topImg']; ?>" width="100%" />
    </div>

    <div class="content">
        <div class="modeTitle"><?php echo $data['title']; ?></div>
        <div class="modeAuthor mt1"><?php echo $data['hospital']; ?> </div>
        <div class="details mt2"><?php echo $data['des']; ?></div>

        <div class="bottom mt3">
            <dl>
                <dt><img src="<?php echo $data['qrcode']; ?>" width="100%" /> </dt>
                <dd class="mt1">长按识别二维码<span style="    color: #737373;
    display: inline-block;
    margin-top: 0.1rem;
}">查看原文</span></dd>
                <div class="logo"><img src="<?php echo $data['logo']; ?>" width="100%" /></div>
            </dl>
        </div>
    </div>

</div>
    <div class="canvasImg"></div>
    <div id="shade" style="width: 100%; height:100%; background-color: #fff; color: #737373; position: absolute; top: 0; text-align: center;line-height: 300px; font-size: 20px">正在生成图片,请稍等...</div>
</div>
<script>
    $(function(){
        var cntElem = $('#contrainer')[0],
            shareContent = cntElem,//需要截图的包裹的（原生的）DOM 对象
            width = shareContent.offsetWidth, //获取dom 宽度
            height = shareContent.offsetHeight,
            scale = 2, //定义任意放大倍数 支持小数
            canvas = document.createElement("canvas");
        canvas.width = width * scale; //定义canvas 宽度 * 缩放
        canvas.height = height * scale; //定义canvas高度 *缩放
        canvas.getContext("2d").scale(scale, scale);
        var opts = {
            scale: scale, // 添加的scale 参数
            canvas: canvas, //自定义 canvas
            // logging: true, //日志开关，便于查看html2canvas的内部执行流程
            width: width, //dom 原始宽度
            height: height,
        };
        html2canvas(shareContent, opts).then(canvas => {
            var context = canvas.getContext('2d');
            // 【重要】关闭抗锯齿
            context.mozImageSmoothingEnabled = false;
            context.webkitImageSmoothingEnabled = false;
            context.msImageSmoothingEnabled = false;
            context.imageSmoothingEnabled = false;
            // 【重要】默认转化的格式为png,也可设置为其他格式
            var img = Canvas2Image.convertToImage(canvas, canvas.width, canvas.height, 'jpg');
            document.getElementById("content").appendChild(img);
            $(img).css({
                "width": canvas.width / 2 + "px",
                "height": canvas.height / 2 + "px",
            }).addClass('f-full');
            if(img){
                $('#contrainer').remove();
                $('#shade').css('display', 'none')
            }
        });
        /*if($('.canvasImg').append(Canvas2Image.convertToImage(canvas, 375, 667, 'png'))){
                $('#contrainer').remove();
                $('#shade').css('display', 'none')
            }*/
    });
</script>
</body>
</html>
