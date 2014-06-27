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
	include_once(EC_PATH . '/includes/lib_order.php');
	
	$order_id = _POST('order_id', 0);
	if (!$order_id) 
	{
		GZ_Api::outPut(101);
	}
	$user_id = $_SESSION['user_id'];

	/* 订单详情 */
	$order = get_order_detail($order_id, $user_id);
	$order_time = _POST('order_time');	

	if ($order['pay_id'] > 0) 
	{
	 	 $payment = payment_info($order['pay_id']);
	}

	if ($payment['pay_code'] == "upop") 
	{
		include_once(GZ_PATH . '/payment/UPMP/upop_mobile.php');
		$upop = new UPOP_MOBILE();
		$pay_result = $upop->query($order,$payment,$order_time); 	
		GZ_Api::outPut($pay_result);
	}
?>