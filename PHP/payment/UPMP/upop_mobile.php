<?php
header ( 'Content-type:text/html;charset=utf-8' );

//require_once("lib/log.class.php");
require_once("lib/upmp_service.php");
/**
 * 类
 */

class UPOP_MOBILE
{
    
    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {    	
		/**
		 * 消费交易-后台
		 */

		//$orderNumber           = $order['order_sn'] . '-' . $this->_formatSN($order['log_id']);	
		$ecmobile_url = ecmobile_url(); 
		$orderNumber  = $order['order_sn'] ;
		$amount = $order['order_amount'];       	

		//需要填入的部分
		$req['version']     		= upmp_config::$version; // 版本号
		$req['charset']     		= upmp_config::$charset; // 字符编码
		$req['transType']   		= "01"; // 交易类型
		$req['merId']       		= upmp_config::$mer_id; // 商户代码
		$req['backEndUrl']      	= $ecmobile_url."/payment/UPMP/notify_url.php"; // 通知URL
		$req['frontEndUrl']     	= $ecmobile_url."/payment/wap_callback.php?code=upop"; // 前台通知URL(可选)
		$req['orderDescription']	= $orderNumber;// 订单描述(可选)
		$req['orderTime']   		= date("YmdHis"); // 交易开始日期时间yyyyMMddHHmmss
		//$req['orderTimeout']   		= ""; // 订单超时时间yyyyMMddHHmmss(可选)
		$req['orderNumber'] 		= $orderNumber; //订单号(商户根据自己需要生成订单号)
		$req['orderAmount'] 		= $amount*100; // 订单金额
		$req['orderCurrency'] 		= "156"; // 交易币种(可选)
		$req['reqReserved'] 		= "透传信息"; // 请求方保留域(可选，用于透传商户信息)

		// 保留域填充方法
		$merReserved['test']   		= "test";
		$req['merReserved']   		= UpmpService::buildReserved($merReserved); // 商户保留域(可选)

		$resp = array ();		
		$validResp = UpmpService::trade($req, $resp);		


		// 商户的业务逻辑
		if ($validResp)
		{
			// 服务器应答签名验证成功
			//print_r($resp);
			//返回结果展示			
			$upop_tn = $resp['tn'];
			$ecmobile_url = ecmobile_url();
			$resultURL = $ecmobile_url."/payment/wap_callback.php?code=upop$argName=";  
			$resultURL = urlencode($resultURL);
			$paydata = "tn=".$upop_tn.",resultURL=".$resultURL.",usetestmode=false";
			$paydata = base64_encode($paydata);
			$pay_url = $ecmobile_url."/payment/wap_upmp.php?paydata=".$paydata;	
			

			return array('upop_tn' => $upop_tn, "pay_url" => $pay_url);
		}
		else 
		{
			// 服务器应答签名验证失败
			//print_r($resp);			
			return array();	
		}						
		

    }

    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function query($order, $payment, $order_time)
    {   
    	$orderNumber  = $order['order_sn'] ;

    	//需要填入的部分
		$req['version']     	= upmp_config::$version; // 版本号
		$req['charset']     	= upmp_config::$charset; // 字符编码
		$req['transType']   	= "01"; // 交易类型
		$req['merId']       	= upmp_config::$mer_id; // 商户代码
		$req['orderTime']   	= $order_time; // 交易开始日期时间yyyyMMddHHmmss或yyyyMMdd
		$req['orderNumber'] 	= $orderNumber; // 订单号

		// 保留域填充方法
		$merReserved['test']   	= "test";
		$req['merReserved']   	= UpmpService::buildReserved($merReserved); // 商户保留域(可选)

		$resp = array ();
		$validResp = UpmpService::query($req, $resp);

		// 商户的业务逻辑
		if ($validResp)
		{
			// 服务器应答签名验证成功
			return $resp;
		}
		else 
		{
			// 服务器应答签名验证失败
			return $resp;
		}
    }

      /**
    * 格式订单号
    */
    function _formatSN($sn)
    {
        return str_repeat('0', 9 - strlen($sn)) . $sn;
    }

}
?>