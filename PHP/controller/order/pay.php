<?php
/*
 *
 *       _/_/_/                      _/        _/_/_/_/_/                     
 *    _/          _/_/      _/_/    _/  _/          _/      _/_/      _/_/    
 *   _/  _/_/  _/_/_/_/  _/_/_/_/  _/_/          _/      _/    _/  _/    _/   
 *  _/    _/  _/        _/        _/  _/      _/        _/    _/  _/    _/    
 *   _/_/_/    _/_/_/    _/_/_/  _/    _/  _/_/_/_/_/    _/_/      _/_/       
 *                                                                          
 *
 *  Copyright 2013-2014, Geek Zoo Studio
 *  http://www.ecmobile.cn/license.html
 *
 *  HQ China:
 *    2319 Est.Tower Van Palace 
 *    No.2 Guandongdian South Street 
 *    Beijing , China
 *
 *  U.S. Office:
 *    One Park Place, Elmira College, NY, 14901, USA
 *
 *  QQ Group:   329673575
 *  BBS:        bbs.ecmobile.cn
 *  Fax:        +86-10-6561-5510
 *  Mail:       info@geek-zoo.com
 */



define('INIT_NO_USERS', true);
require(EC_PATH . '/includes/init.php');
GZ_Api::authSession();

include_once(EC_PATH . '/includes/lib_transaction.php');
include_once(EC_PATH . '/includes/lib_payment.php');
include_once(EC_PATH . '/includes/lib_order.php');
include_once(EC_PATH . '/includes/lib_clips.php');
define('GZ_PATH', dirname(__FILE__));

$order_id = _POST('order_id', 0);
if (!$order_id) {
	GZ_Api::outPut(101);
}
$user_id = $_SESSION['user_id'];


/* 订单详情 */
$order = get_order_detail($order_id, $user_id);
if ($order['pay_id'] > 0) 
{
  $payment = payment_info($order['pay_id']);
}

if ($order === false)
{
	GZ_Api::outPut(8);
}	

$base = sprintf('<base href="%s/" />', dirname($GLOBALS['ecs']->url()));
$html = '<!DOCTYPE html><html><head><title></title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0">'.$base.'</head><body>%s</body></html>';

if ($payment['pay_code'] == "upop") 
{
	include_once(GZ_PATH . '/payment/UPMP/upop_mobile.php');
	$upop = new UPOP_MOBILE();
	$pay_result = $upop->get_code($order,$payment); 
	$out =array('pay_online' => sprintf($html, $order['pay_online']));	

	if ($pay_result['upop_tn']) 
	{
		$out['upop_tn'] = $pay_result['upop_tn'];
		$out['pay_wap'] = $pay_result['pay_url'];
	}	

	GZ_Api::outPut($out);
}
else if ($payment['pay_code'] == "alipay") 
{		
	include_once(GZ_PATH . '/payment/alipay/wap/alipay_mobile.php');
	require_once(GZ_PATH . "/payment/alipay/wap/alipay.config.php");
	$alipay_mobile = new ALIPAY_MOBILE($alipay_config);
	$wappay_url = $alipay_mobile->get_wappay_url($order,$payment);
	if ($wappay_url) 
	{
		GZ_Api::outPut(array('pay_online' => sprintf($html, $order['pay_online']),							
							'pay_wap' => $wappay_url
							));
	}
	else
	{		
		GZ_Api::outPut(array('pay_online' => sprintf($html, $order['pay_online'])								
								));
	}	
}
else
{
	GZ_Api::outPut(array('pay_online' => sprintf($html, $order['pay_online'])));
}




?>