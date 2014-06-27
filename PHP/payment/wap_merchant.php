<?php

define('IN_ECS', true);
define('GZ_PATH', dirname(dirname(__FILE__)));
define('EC_PATH', dirname(GZ_PATH));
require_once(EC_PATH . '/includes/init.php');
require_once(GZ_PATH. "/library/function.php");

//echo '操作中断';
$url = ecmobile_url().'/app_callback.php?err=2&order_id='. $_GET['out_trade_no'];
header('Location:'.$url);
  
?>