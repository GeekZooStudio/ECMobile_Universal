<?php
/**
 * 类名：交易服务类
 * 功能：接口公用函数类
 * 版本：1.0
 * 日期：2012-10-11
 * 作者：中国银联UPMP团队
 * 版权：中国银联
 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
 * */

require_once(__DIR__."/../conf/upmp_config.php");

/**
 * 除去请求要素中的空值和签名参数
 * @param para 请求要素
 * @return 去掉空值与签名参数后的请求要素
 */
function paraFilter($para) {
	$result = array ();
	while ( list ( $key, $value ) = each ( $para ) ) {
		if ($key == upmp_config::SIGNATURE || $key == upmp_config::SIGN_METHOD || $value == "") {
			continue;
		} else {
			$result [$key] = $para [$key];
		}
	}
	return $result;
}

/**
 * 生成签名
 * @param req 需要签名的要素
 * @return 签名结果字符串
 */
function buildSignature($req) {
	$prestr = createLinkstring($req, true, false);
	$prestr = $prestr.upmp_config::QSTRING_SPLIT.md5(upmp_config::$security_key);
	return md5($prestr);
}

/**
 * 把请求要素按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param para 请求要素
 * @param sort 是否需要根据key值作升序排列
 * @param encode 是否需要URL编码
 * @return 拼接成的字符串
 */
function createLinkString($para, $sort, $encode) {
	$linkString  = "";
	if ($sort){
		$para = argSort($para);
	}
	while (list ($key, $value) = each ($para)) {
		if ($encode){
			$value = urlencode($value);
		}
		$linkString.=$key.upmp_config::QSTRING_EQUAL.$value.upmp_config::QSTRING_SPLIT;
	}
	//去掉最后一个&字符
	$linkString = substr($linkString,0,count($linkString)-2);
	
	return $linkString;
}

/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}

/*
 * curl_call
*
* @url:  string, curl url to call, may have query string like ?a=b
* @content: array(key => value), data for post
*
* return param:
*	mixed:
*	  false: error happened
*	  string: curl return data
*
*/
function post($url, $content = null)
{
	if (function_exists("curl_init")) {
		$curl = curl_init();

		if (is_array($content)) {
			$data = http_build_query($content);
		}

		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 60); //seconds
		
		// https verify
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, upmp_config::VERIFY_HTTPS_CERT);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, upmp_config::VERIFY_HTTPS_CERT);

		$ret_data = curl_exec($curl);

		if (curl_errno($curl)) {
			printf("curl call error(%s): %s\n", curl_errno($curl), curl_error($curl));
			curl_close($curl);
			return false;
		}
		else {
			curl_close($curl);
			return $ret_data;
		}
	} else {
		throw new Exception("[PHP] curl module is required");
	}
}

?>