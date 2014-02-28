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

$id = _POST('address_id', 0);

include_once(EC_PATH . '/includes/lib_order.php');
include_once(EC_PATH . '/includes/lib_transaction.php');

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
        " WHERE address_id = '$id'";

$arr = $GLOBALS['db']->getRow($sql);

$consignee = get_consignee($user_id);// 取得默认地址

$result["id"] = $arr['address_id'];
$result["consignee"] = $arr['consignee'];
$result["email"] = $arr['email'];

$result["country"] = $arr['country'];
$result["province"] = $arr['province'];
$result["city"] = $arr['city'];
$result["district"] = $arr['district'];

$ids = array($result["country"], $result["province"], $result["city"], $result["district"]);
$ids = array_filter($ids);

$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('region') .
    " WHERE region_id IN(".implode(',', $ids).')';
$data = $GLOBALS['db']->getAll($sql);
$out = array();
foreach ($data as $key => $val) {
    $out[$val['region_id']] = $val['region_name'];
}

$result["country_name"] = isset($out[$result["country"]]) ? $out[$result["country"]] : '';
$result["province_name"] = isset($out[$result["province"]]) ? $out[$result["province"]] : '';
$result["city_name"] = isset($out[$result["city"]]) ? $out[$result["city"]] : '';
$result["district_name"] = isset($out[$result["district"]]) ? $out[$result["district"]] : '';

$result["address"] = $arr['address'];
$result["zipcode"] = $arr['zipcode'];
$result["mobile"] = $arr['mobile'];
$result["sign_building"] = $arr['sign_building'];
$result["best_time"] = $arr['best_time'];
$result["default_address"] = $arr['default_address'];
$result["tel"] = $arr['tel'];

if ( $arr['address_id'] == $consignee['address_id'] ) {
	$result['default_address'] = 1;
} else {
	$result['default_address'] = 0;
}

GZ_Api::outPut($result);