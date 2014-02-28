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

require(EC_PATH . '/includes/init.php');
include_once(EC_PATH . '/includes/lib_order.php');

$name = _POST('name');
$password = _POST('password');
 
if (!$user->login($name, $password)) {
	GZ_Api::outPut(6);
}

$user_info = GZ_user_info($_SESSION['user_id']);

$out = array(
	'session' => array(
		'sid' => SESS_ID.$GLOBALS['sess']->gen_session_key(SESS_ID),
		'uid' => $_SESSION['user_id']
	),

	'user' => $user_info
);

update_user_info();
recalculate_price();

GZ_Api::outPut($out);
