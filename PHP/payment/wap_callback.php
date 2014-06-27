<?php

define('IN_ECS', true);
define('GZ_PATH', dirname(dirname(__FILE__)));
define('EC_PATH', dirname(GZ_PATH));
require_once(EC_PATH . '/includes/init.php');
require_once(GZ_PATH. "/library/function.php");

$code = $_GET['code'];
unset($_GET['code']);
switch ($code) {
    case 'alipay':
        alipay_callback();
        break;
    case 'tenpay':
        break;
    case 'unionpay':
        unionpay_callback();
        break;
    default:
        # code...
        break;
}

function alipay_callback()
{
    require_once("alipay/wap/alipay.config.php");
    require_once("alipay/wap/lib/alipay_notify.class.php");

    $out_trade_no = $_GET['out_trade_no']; //商户订单号
    $trade_no = $_GET['trade_no']; //支付宝交易号
    $result = $_GET['result']; //交易状态

    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();
    if($verify_result) {
        goto_app_callback(0, $out_trade_no);
    }
    else {
        //验证失败
        //echo "验证失败";
        goto_app_callback(1, $out_trade_no);
    }
}

function unionpay_callback()
{
    include_once EC_PATH . '/payment/UPMP/utf8/mpi/MpiConstants.php';
    include_once EC_PATH . '/payment/UPMP/utf8/func/common.php';
    include_once EC_PATH . '/payment/UPMP/utf8/func/secureUtil.php';

    if (isset ( $_REQUEST ['signature'] )) 
    {
        if (verify ( $_REQUEST ) ) 
        {
            goto_app_callback(0, $out_trade_no);
            return;
        }
        else
        {
            goto_app_callback(1, $out_trade_no);
        }
    }
}

function goto_app_callback($err, $order_id) {
    $url = ecmobile_url().'/app_callback.php?err=' . $err . '&order_id='. $order_id;
    echo '<meta http-equiv="refresh" content="1;url='.$url.'">';
    //header('Location:'.$url);
}

?>