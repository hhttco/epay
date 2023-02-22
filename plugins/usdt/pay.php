<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="renderer" content="webkit">
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <link rel="shortcut icon" href="/assets/usdt/tether.svg"/>
    <title>USDT 在线收银台</title>
    <link href="/assets/usdt/main.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container" style="margin-top: -10%;">
    <div class="header" style="width: 50%;">
        <div class="icon">
            <img class="logo" src="/assets/usdt/tether.svg" alt="logo">
        </div>
        <p style="color: #3C8CE7 ;font-size: 18px;font-weight: 700; text-align: center;margin: 20px 0;">
            支付方式：[ USDT(TRC20) ], 请打开 APP 扫码支付！或点击复制地址！有效期20分钟
        </p>
    </div>
    <div class="content" style="width: 50%;">
        <div class="section">
            <p class="product-pay-price" style="font-size: 16px;color: red;">
                到账金额<strong>必须相同</strong>，否则订单将无效
            </p>
            <div class="title">
                <h1 class="amount parse-amount" data-clipboard-text="<?= $usdt; ?>" id="usdt">
                    <?= $usdt; ?> <span>USDT.TRC20</span>
                </h1>
            </div>
            <div data-clipboard-text="<?= $address; ?>" id="address">
                <div class="address parse-action">
                    <?= $address; ?>
                </div>
                <div class="main">
                    <div class="qr-image" id="qrcode"></div>
                </div>
            </div>
            <div class="timer">
                <ul class="downcount">
                    <li>
                        <span class="hours">00</span>
                        <p class="hours_ref">时</p>
                    </li>
                    <li class="seperator">:</li>
                    <li>
                        <span class="minutes">00</span>
                        <p class="minutes_ref">分</p>
                    </li>
                    <li class="seperator">:</li>
                    <li>
                        <span class="seconds">00</span>
                        <p class="seconds_ref">秒</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- <div class="footer">
        <p>Powered by <a href="" target="_blank">测试</a></p>
    </div> -->
</div>
<script src="/assets/usdt/jquery.min.js"></script>
<script src="/assets/usdt/clipboard.min.js"></script>
<script src="/assets/js/jquery-qrcode.min.js"></script>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
    // 检查是否支付完成
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/getshop.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {type: "alipay", trade_no: "<?php echo $order['trade_no']?>"}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.1, time: 15000});
                    setTimeout(window.location.href = data.backurl, 1000);
                } else {
                    setTimeout("loadmsg()", 2000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("loadmsg()", 1000);
                } else { //异常
                    setTimeout("loadmsg()", 3000);
                }
            }
        });
    }

    function checkresult() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/getshop.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {type: "alipay", trade_no: "<?php echo $order['trade_no']?>"},
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
                    layer.msg('支付成功，正在跳转中...', {icon: 16, shade: 0.1, time: 15000});
                    setTimeout(window.location.href = data.backurl, 1000);
                } else {
                    layer.msg('您还未完成付款，请继续付款', {shade: 0, time: 1500});
                }
            }
        });
    }

    $(function () {
        $('#qrcode').qrcode({
            text: "<?= $address; ?>",
            width: 230,
            height: 230,
            foreground: "#000000",
            background: "#ffffff",
            typeNumber: -1
        });

        (new Clipboard('#usdt')).on('success', function (e) {
            layer.msg('金额复制成功');
        });
        (new Clipboard('#address')).on('success', function (e) {
            layer.msg('地址复制成功');
        });

        // 支付时间倒计时
        function clock() {
            let timeout = new Date(<?=$valid; ?>);
            let now = new Date();
            let ms = timeout.getTime() - now.getTime();//时间差的毫秒数
            let second = Math.round(ms / 1000);
            let minute = Math.floor(second / 60);
            let hour = Math.floor(minute / 60);
            if (ms <= 0) {
                layer.alert("支付超时，请重新发起支付！", {icon: 5});
                return;
            }

            $('.hours').text(hour.toString().padStart(2, '0'));
            $('.minutes').text(minute.toString().padStart(2, '0'));
            $('.seconds').text((second % 60).toString().padStart(2, '0'));

            return setTimeout(clock, 1000);
        }

        setTimeout(clock, 1000);

        setTimeout("loadmsg()", 2000);
    });
</script>
</body>
</html>
