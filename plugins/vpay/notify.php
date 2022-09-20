<?php
if(!defined('IN_PLUGIN'))exit();
ini_set("error_reporting","E_ALL & ~E_NOTICE");
$key = $channel['appkey'];//通讯密钥
$payId = $_GET['payId'];//商户订单号
$param = $_GET['param'];//创建订单的时候传入的参数
$type = $_GET['type'];//支付方式 ：微信支付为1 支付宝支付为2
$price = $_GET['price'];//订单金额
$reallyPrice = $_GET['reallyPrice'];//实际支付金额
$sign = $_GET['sign'];//校验签名，计算方式 = md5(payId + param + type + price + reallyPrice + 通讯密钥)
//开始校验签名
$_sign =  md5($payId . $param . $type . $price . $reallyPrice . $key);
if ($_sign != $sign) {
    echo "error_sign";//sign校验不通过
    exit();
}

$out_trade_no = daddslashes($payId);

//流水号
$trade_no = daddslashes($out_trade_no.time());

$price = $reallyPrice;

if($out_trade_no == TRADE_NO && round($price,2)>=round($order['money']-1,2) && $order['status']==0){
	if($DB->exec("update `pre_order` set `status` ='1' where `trade_no`='$out_trade_no'")){
		$DB->exec("update `pre_order` set `api_trade_no` ='$trade_no',`endtime` ='$date',`date` =NOW() where `trade_no`='$out_trade_no'");
		processOrder($order);
	}
}

echo "success";