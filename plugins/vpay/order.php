<?php
if(!defined('IN_PLUGIN'))exit();
include(PAY_ROOT."config.php");
$typename = $DB->getColumn("SELECT name FROM pre_type WHERE id='{$order['type']}' LIMIT 1");
if ($typename == 'wxpay') {
	$typeName = '微信';
    $type = 1;
    $css='wechat_pay';
    $ico='wechat-pay';
} else {
    $type = 2;
    $typeName = '支付宝';
    $css='alipay_pay';
    $ico='alipay-pay';
}

$data = array(
	'payId'=>TRADE_NO,
	'type'=>$type,
	'price'=>$order['money'],
	'isHtml'=>'2',
	"notifyUrl" => $siteurl.'pay/vpay/notify/'.TRADE_NO.'/',//异步通知地址
	"returnUrl" => $siteurl.'pay/vpay/return/'.TRADE_NO.'/'//同步跳转地址
);

$data['sign']=md5($data['payId'].$data['type'].$data['price'].$channel['appkey']);

$url = $channel['appid']."createOrder?".http_build_query($data); //支付页面
$returnJson=get_curl($url);
$returnJson=json_decode($returnJson,true);

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="zh-cn">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $typeName?>安全支付 - <?php echo $sitename?></title>
<link href="/assets/css/<?php echo $css?>.css" rel="stylesheet" media="screen">
<style>
.mod-title .ico-wechat {
    display: inline-block;
    width: 41px;
    height: 36px;
    background: url(/assets/css/<?php echo $ico?>.png) 0 -115px no-repeat;
    vertical-align: middle;
    margin-right: 7px;
}
.mod-ct{
    max-width: 610px;
    width: auto;
}
#qrcode{
    color:red;
    font-weight: bold;
}
@media (max-width: 960px) {
  .mod-ct{
    padding: 0 15px;
  }
}
</style>
</head>
<body>
<div class="body">
<h1 class="mod-title">
<span class="ico-wechat"></span><span class="text"><?php echo $typeName?>支付</span>
</h1>
<div class="mod-ct">
<div class="order">
</div>
<?php
    if($returnJson['code']!=1){
        echo '<div style="color:red;text-align:center;padding:20px 0 40px;">出错了, 稍后再试'.$returnJson['msg'].'</div>';exit;
    }
?>
<div class="amount">￥<?php echo $returnJson['data']['reallyPrice']?></div>
<div class="qr-image" id="qrcode">
</div>
<?php if($typename == 'wxpay'){?>

<?php }else{?>
<a style="display:block;margin-top:20px;font-size:16px;" href="alipays://platformapi/startapp?appId=20000067&url=<?php echo $returnJson['data']['payUrl']?>">立即支付</a>
<?php }?>
<div class="detail" id="orderDetail">
<dl class="detail-ct" style="display: none;">
<dt>商家</dt>
<dd id="storeName"><?php echo $sitename?></dd>
<dt>购买物品</dt>
<dd id="productName"><?php echo $order['name']?></dd>
<dt>商户订单号</dt>
<dd id="billId"><?php echo $order['trade_no']?></dd>
<dt>创建时间</dt>
<dd id="createTime"><?php echo $order['addtime']?></dd>
</dl>
<a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
</div>
<div class="tip">
<span class="dec dec-left"></span>
<span class="dec dec-right"></span>
<div class="ico-scan"></div>
<div class="tip-text">
<p>请使用<?php echo $typeName?>扫一扫</p>
<p>扫描二维码完成支付</p>
</div>
</div>
<div class="tip-text">
</div>
</div>
<div class="foot">
<div class="inner">
<p>手机用户可保存上方二维码到手机中</p>
<p>在微信扫一扫中选择“相册”即可</p>
</div>
</div>
</div>
<script src="/assets/js/qcloud_util.js"></script>
<script src="/assets/js/jquery-qrcode.min.js"></script>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<script>
    setTimeout(function(){
        $('#qrcode').html('已过期');
    },5*50*1000);
    $('#qrcode').qrcode({
        text: "<?php echo $returnJson['data']['payUrl']?>",
        width: 230,
        height: 230,
        foreground: "#000000",
        background: "#ffffff",
        typeNumber: -1
    });
    // 订单详情
    $('#orderDetail .arrow').click(function (event) {
        if ($('#orderDetail').hasClass('detail-open')) {
            $('#orderDetail .detail-ct').slideUp(500, function () {
                $('#orderDetail').removeClass('detail-open');
            });
        } else {
            $('#orderDetail .detail-ct').slideDown(500, function () {
                $('#orderDetail').addClass('detail-open');
            });
        }
    });
    // 检查是否支付完成
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/getshop.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {type: "wxpay", trade_no: "<?php echo $order['trade_no']?>"}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 1) {
					layer.msg('支付成功，正在跳转中...', {icon: 16,shade: 0.01,time: 15000});
                    window.location.href=data.backurl;
                }else{
                    setTimeout("loadmsg()", 4000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("loadmsg()", 1000);
                } else { //异常
                    setTimeout("loadmsg()", 4000);
                }
            }
        });
    }
    window.onload = loadmsg();
</script>

</body>
</html>