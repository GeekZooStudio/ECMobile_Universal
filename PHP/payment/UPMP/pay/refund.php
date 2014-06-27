<?php

/**
 * 类名：退货接口实例类文件
 * 功能：退货接口实例
 * 版本：1.0
 * 日期：2012-10-11
 * 作者：中国银联UPMP团队
 * 版权：中国银联
 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
 * */
header('Content-Type:text/html;charset=utf-8');
require_once("../lib/upmp_service.php");

//需要填入的部分
$req['version']     	= upmp_config::$version; // 版本号
$req['charset']     	= upmp_config::$charset; // 字符编码
$req['transType']   	= "04"; // 交易类型
$req['merId']       	= upmp_config::$mer_id; // 商户代码
$req['backEndUrl']      = upmp_config::$mer_back_end_url; // 通知URL
$req['orderTime']   	= date("YmdHis"); // 交易开始日期时间yyyyMMddHHmmss（退货交易新交易日期，非原交易日期）
$req['orderNumber'] 	= date("YmdHiss"); // 订单号（退货交易新订单号，非原交易订单号）
$req['orderAmount'] 	= "1"; // 订单金额
$req['orderCurrency'] 	= "156"; // 交易币种(可选)
$req['qn'] 				= ""; // 查询流水号（原订单支付成功后获取的流水号）
$req['reqReserved'] 	= "透传信息"; // 请求方保留域(可选，用于透传商户信息)

// 保留域填充方法
$merReserved['test']   	= "test";
$req['merReserved']   	= UpmpService::buildReserved($merReserved); // 商户保留域(可选)

$resp = array ();
$validResp = UpmpService::trade($req, $resp);

// 商户的业务逻辑
if ($validResp){
	// 服务器应答签名验证成功
	print_r($resp);
}else {
	// 服务器应答签名验证失败
	print_r($resp);
}

?>
