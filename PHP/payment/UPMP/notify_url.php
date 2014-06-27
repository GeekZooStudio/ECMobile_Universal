<?php
 /**
  * 功能：异步通知页面
  * 版本：1.0
  * 日期：2012-10-11
  * 作者：中国银联UPMP团队
  * 版权：中国银联
  * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
  * */

header('Content-Type:text/html;charset=utf-8');
define('IN_ECS', true);

require_once("lib/upmp_service.php");
require($_SERVER ['DOCUMENT_ROOT'] . '/includes/init.php');
require($_SERVER ['DOCUMENT_ROOT'] . '/includes/lib_payment.php');
require($_SERVER ['DOCUMENT_ROOT'] . '/includes/lib_order.php');
require($_SERVER ['DOCUMENT_ROOT'] . '/includes/lib_clips.php');

if (UpmpService::verifySignature($_POST))
{// 服务器签名验证成功
	//请在这里加上商户的业务逻辑程序代码
	//获取通知返回参数，可参考接口文档中通知参数列表(以下仅供参考)
	$transStatus = $_POST['transStatus'];// 交易状态
	if (""!=$transStatus && "00"==$transStatus)
	{
		// 交易处理成功
		logResult("交易处理成功");
		logResult(var_export($_POST, true));
		$orderNumber = $_POST['orderNumber'];

	    logResult('orderNumber:'.$orderNumber);

	    $order = order_info(0, $orderNumber);	    

	    if ($order) 
	    {
	        logResult('order_id:'.$order['order_id']);
	        $log_id = insert_pay_log($order['order_id'], $order['order_amount'], PAY_ORDER);
	        logResult('log_id:'.$log_id);
	        order_paid($log_id, 2);
	    }
	}
	else 
	{
		logResult("交易处理失败");
		logResult(var_export($_POST, true));
	}
	echo "success";
}
else 
{// 服务器签名验证失败
	logResult("服务器签名验证失败");
	echo "fail";
}

/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word='') 
{
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}

?>