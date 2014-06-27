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

//get方式验证session
GZ_Api::$session = array('uid' => _GET('uid'), 'sid' => _GET('sid'));
GZ_Api::authSession();

$code = trim(_GET('code'));
$order_id = _GET('order_id');
$order_desc = _GET('order_desc');
$amount = _GET('amount');
$currency = _GET('currency');

/*
$code = 'alipay';
$order_id = date('YmdHis', time());
$order_desc = 'ipod';
$amount = 0.01;
*/

$ecmobile_url = ecmobile_url();

if (empty($code) || empty($order_id) || empty($order_desc) || empty($amount)) {
    self::outPut(101);
}

switch ($code) {
    case 'alipay':
        require_once(GZ_PATH. "/payment/alipay/alipay.config.php");
        require_once(GZ_PATH. "/payment/alipay/alipay_submit.class.php");

        //修正配置文件路径
        $alipay_config = modify_config_path($alipay_config);

        $format = "xml";  //返回格式
        $v = "2.0";
        $req_id = date('Ymdhis');  //请求号
        $notify_url = $ecmobile_url."/payment/wap_notify.php?code=alipay";  //服务器异步通知页面路径
        $call_back_url = $ecmobile_url."/payment/wap_callback.php?code=alipay";  //页面跳转同步通知页面路径
        $merchant_url = $ecmobile_url."/payment/wap_merchant.php?code=alipay";  //操作中断返回地址
        $seller_email = 'pay@geek-zoo.com';  //卖家支付宝帐户
        $out_trade_no = $order_id;  //商户订单号
        $subject =  $order_desc; //订单名称
        $total_fee =  $amount;  //付款金额

        //请求业务参数详细
        $req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
        $para_token = array(
            "service" => "alipay.wap.trade.create.direct",
            "partner" => trim($alipay_config['partner']),
            "sec_id" => trim($alipay_config['sign_type']),
            "format"    => $format,
            "v" => $v,
            "req_id"    => $req_id,
            "req_data"  => $req_data,
            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($para_token);
        $html_text = urldecode($html_text);
        //解析远程模拟提交后返回的信息
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        //获取request_token
        $request_token = $para_html_text['request_token'];
        //业务详细
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        //构造要请求的参数数组，无需改动
        $parameter = array(
                "service" => "alipay.wap.auth.authAndExecute",
                "partner" => trim($alipay_config['partner']),
                "sec_id" => trim($alipay_config['sign_type']),
                "format"    => $format,
                "v" => $v,
                "req_id"    => $req_id,
                "req_data"  => $req_data,
                "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $url = $alipaySubmit->alipay_gateway_new.$alipaySubmit->buildRequestParaToString($parameter);
        header("Location: $url");
        //$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
        //echo $html_text;
        break;
    default:
        GZ_Api::outPut(101);
        break;
}

function modify_config_path($alipay_config)
{
    $alipay_config['cacert'] =  str_replace('/alipay', '/payment/alipay', $alipay_config['cacert']);
    return $alipay_config;
}

?>