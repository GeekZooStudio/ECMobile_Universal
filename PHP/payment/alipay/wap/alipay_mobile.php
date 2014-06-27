<?php
header ( 'Content-type:text/html;charset=utf-8' );
require_once(GZ_PATH. "/payment/alipay/wap/lib/alipay_submit.class.php");
/**
 * 类
 */


class ALIPAY_MOBILE
{
    public $alipay_config;

    function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;        
    }

    function ALIPAY_MOBILE($alipay_config) 
    {
        $this->__construct($alipay_config);
    }
    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_wappay_url($order, $payment)
    {  
        $order_id = $order['order_id'];
        $order_desc = $order['order_sn'];
        $order_sn = $order['order_sn'];
        $amount = $order['order_amount'];        
                
    	$ecmobile_url = ecmobile_url();        
        //修正配置文件路径
        $this->alipay_config = $this->modify_config_path($this->alipay_config);          

        $format = "xml";  //返回格式
        $v = "2.0";
        $req_id = date('Ymdhis');  //请求号
        $notify_url = $ecmobile_url."/payment/alipay/wap/notify_url.php";  //服务器异步通知页面路径
        $call_back_url = $ecmobile_url."/payment/wap_callback.php?code=alipay";  //页面跳转同步通知页面路径
        $merchant_url = $ecmobile_url."/payment/wap_merchant.php?code=alipay";  //操作中断返回地址
        $seller_email = 'pay@geek-zoo.com';  //卖家支付宝帐户
        $out_trade_no = $order_sn;  //商户订单号
        $subject =  $order_desc; //订单名称
        $total_fee =  $amount;  //付款金额

        //请求业务参数详细
        $req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
        $para_token = array(
            "service" => "alipay.wap.trade.create.direct",
            "partner" => trim($this->alipay_config['partner']),
            "sec_id" => trim($this->alipay_config['sign_type']),
            "format"    => $format,
            "v" => $v,
            "req_id"    => $req_id,
            "req_data"  => $req_data,
            "_input_charset"    => trim(strtolower($this->alipay_config['input_charset']))
        );        
        //建立请求
        $alipaySubmit = new AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($para_token);
        $html_text = urldecode($html_text);
        //解析远程模拟提交后返回的信息
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        //获取request_token
        $request_token = $para_html_text['request_token'];
        if ($request_token) 
        {
                        //业务详细
            $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
            //构造要请求的参数数组，无需改动
            $parameter = array(
                    "service" => "alipay.wap.auth.authAndExecute",
                    "partner" => trim($this->alipay_config['partner']),
                    "sec_id" => trim($this->alipay_config['sign_type']),
                    "format"    => $format,
                    "v" => $v,
                    "req_id"    => $req_id,
                    "req_data"  => $req_data,
                    "_input_charset"    => trim(strtolower($this->alipay_config['input_charset']))
            );

            //建立请求
            $alipaySubmit = new AlipaySubmit($this->alipay_config);
            $url = $alipaySubmit->alipay_gateway_new.$alipaySubmit->buildRequestParaToString($parameter);
            return $url;
        }
        else
        {
            return false;
        }

    }

    function modify_config_path($alipay_config)
	{
	    $alipay_config['cacert'] =  str_replace('/alipay', '/payment/alipay', $alipay_config['cacert']);
	    return $alipay_config;
	}
}

?>      	