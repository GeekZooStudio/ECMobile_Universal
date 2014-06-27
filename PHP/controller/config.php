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

define('DEBUG_MODE', 7);

define('ROOT_PATH', EC_PATH . '/');

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

// 通用包含文件
require(ROOT_PATH . 'data/config.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/cls_mysql.php');
/* 兼容ECShopV2.5.1版本载入库文件 */
if (!function_exists('addslashes_deep'))
{
    require(ROOT_PATH . 'includes/lib_base.php');
}

/* 兼容ECShopV2.5.1版本 */
if (!defined('EC_CHARSET'))
{
    define('EC_CHARSET', 'utf-8');
}


/* 初始化包含文件 */
require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_insert.php');
require(ROOT_PATH . 'includes/lib_goods.php');

/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);

/* 初始化数据库类 */
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 载入系统参数 */
$_CFG = load_config();


$data = array(
    'service_phone' => $_CFG['service_phone'],
    'site_url' => dirname($GLOBALS['ecs']->url()),
    'goods_url' => dirname($GLOBALS['ecs']->url()).'/goods.php?id=',
    'shop_closed' => $_CFG['shop_closed'],
    'close_comment' => $_CFG['close_comment'],
    'shop_reg_closed' => $_CFG['shop_reg_closed'],
    'shop_desc' => $_CFG['shop_desc'],
    'currency_format' => $_CFG['currency_format'],
    "time_format" => $_CFG['time_format']
);


GZ_Api::outPut(array('data' => $data));