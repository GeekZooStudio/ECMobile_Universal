<?php
/*
 *
 * Created by quqiang <77347042@qq.com> on 2013-06-07.
 *
 */





define('INIT_NO_USERS', true);
require(EC_PATH . '/includes/init.php');
GZ_Api::authSession();
include_once(EC_PATH . '/includes/lib_transaction.php');
include_once(EC_PATH . '/includes/lib_order.php');

$AppKey = _POST('app_key', '');
$order_id = _POST('order_id', 0);

if (empty($order_id)) {
	GZ_Api::outPut(101);
}

$order_info = order_info($order_id);

if (!$order_info || empty($order_info['shipping_name']) || empty($order_info['invoice_no'])) {
	GZ_Api::outPut(10009);
}

// $order_info['invoice_no'] = '3180299401';  test quq
// $order_info['shipping_name'] = '圆通速递';

$typeCom = getComType($order_info['shipping_name']);//快递公司类型

if (empty($typeCom)) {
	GZ_Api::outPut(10009);
}
$url = 'http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$order_info['invoice_no'].'&valicode=[]&show=0&muti=1&order=asc';
$json =  file_get_contents($url);
$data = json_decode($json, true);
if (empty($data)) {
	GZ_Api::outPut(array('content'=>array('context'=>'无物流记录', 'time'=>''), 'shipping_name'=>$order_info['shipping_name']));
} else {
	$data['data'] = array_reverse($data['data']);
}
$out = array();
$out['content'] = $data['data'];
$out['shipping_name'] = $order_info['shipping_name'];

GZ_Api::outPut($out);




function getComType($typeCom)
{
	if ($typeCom == 'AAE全球专递'){
		$typeCom = 'aae';
	}elseif ($typeCom == '安捷快递'){
		$typeCom = 'anjiekuaidi';
	}elseif ($typeCom == '安信达快递'){
		$typeCom = 'anxindakuaixi';
	}elseif ($typeCom == '百福东方'){
		$typeCom = 'baifudongfang';
	}elseif ($typeCom == '彪记快递'){
		$typeCom = 'biaojikuaidi';
	}elseif ($typeCom == 'BHT'){
		$typeCom = 'bht';
	}elseif ($typeCom == '希伊艾斯快递'){
		$typeCom = 'cces';
	}elseif ($typeCom == '中国东方'){
		$typeCom = 'coe';
	}elseif ($typeCom == '长宇物流'){
		$typeCom = 'changyuwuliu';
	}elseif ($typeCom == '大田物流'){
		$typeCom = 'datianwuliu';
	}elseif ($typeCom == '德邦物流'){
		$typeCom = 'debangwuliu';
	}elseif ($typeCom == 'DPEX'){
		$typeCom = 'dpex';
	}elseif ($typeCom == 'DHL'){
		$typeCom = 'dhl';
	}elseif ($typeCom == 'D速快递'){
		$typeCom = 'dsukuaidi';
	}elseif ($typeCom == 'fedex'){
		$typeCom = 'fedex';
	}elseif ($typeCom == '飞康达物流'){
		$typeCom = 'feikangda';
	}elseif ($typeCom == '凤凰快递'){
		$typeCom = 'fenghuangkuaidi';
	}elseif ($typeCom == '港中能达物流'){
		$typeCom = 'ganzhongnengda';
	}elseif ($typeCom == '广东邮政物流'){
		$typeCom = 'guangdongyouzhengwuliu';
	}elseif ($typeCom == '汇通快运'){
		$typeCom = 'huitongkuaidi';
	}elseif ($typeCom == '恒路物流'){
		$typeCom = 'hengluwuliu';
	}elseif ($typeCom == '华夏龙物流'){
		$typeCom = 'huaxialongwuliu';
	}elseif ($typeCom == '佳怡物流'){
		$typeCom = 'jiayiwuliu';
	}elseif ($typeCom == '京广速递'){
		$typeCom = 'jinguangsudikuaijian';
	}elseif ($typeCom == '急先达'){
		$typeCom = 'jixianda';
	}elseif ($typeCom == '佳吉物流'){
		$typeCom = 'jiajiwuliu';
	}elseif ($typeCom == '加运美'){
		$typeCom = 'jiayunmeiwuliu';
	}elseif ($typeCom == '快捷速递'){
		$typeCom = 'kuaijiesudi';
	}elseif ($typeCom == '联昊通物流'){
		$typeCom = 'lianhaowuliu';
	}elseif ($typeCom == '龙邦物流'){
		$typeCom = 'longbanwuliu';
	}elseif ($typeCom == '民航快递'){
		$typeCom = 'minghangkuaidi';
	}elseif ($typeCom == '配思货运'){
		$typeCom = 'peisihuoyunkuaidi';
	}elseif ($typeCom == '全晨快递'){
		$typeCom = 'quanchenkuaidi';
	}elseif ($typeCom == '全际通物流'){
		$typeCom = 'quanjitong';
	}elseif ($typeCom == '全日通快递'){
		$typeCom = 'quanritongkuaidi';
	}elseif ($typeCom == '全一快递'){
		$typeCom = 'quanyikuaidi';
	}elseif ($typeCom == '盛辉物流'){
		$typeCom = 'shenghuiwuliu';
	}elseif ($typeCom == '速尔物流'){
		$typeCom = 'suer';
	}elseif ($typeCom == '盛丰物流'){
		$typeCom = 'shengfengwuliu';
	}elseif ($typeCom == '天地华宇'){
		$typeCom = 'tiandihuayu';
	}elseif ($typeCom == '天天'){
		$typeCom = 'tiantian';
	}elseif ($typeCom == 'TNT'){
		$typeCom = 'tnt';
	}elseif ($typeCom == 'UPS'){
		$typeCom = 'ups';
	}elseif ($typeCom == '万家物流'){
		$typeCom = 'wanjiawuliu';
	}elseif ($typeCom == '文捷航空速递'){
		$typeCom = 'wenjiesudi';
	}elseif ($typeCom == '伍圆速递'){
		$typeCom = 'wuyuansudi';
	}elseif ($typeCom == '万象物流'){
		$typeCom = 'wanxiangwuliu';
	}elseif ($typeCom == '新邦物流'){
		$typeCom = 'xinbangwuliu';
	}elseif ($typeCom == '信丰物流'){
		$typeCom = 'xinfengwuliu';
	}elseif ($typeCom == '星晨急便'){
		$typeCom = 'xingchengjibian';
	}elseif ($typeCom == '鑫飞鸿物流快递'){
		$typeCom = 'xinhongyukuaidi';
	}elseif ($typeCom == '亚风速递'){
		$typeCom = 'yafengsudi';
	}elseif ($typeCom == '一邦速递'){
		$typeCom = 'yibangwuliu';
	}elseif ($typeCom == '优速物流'){
		$typeCom = 'youshuwuliu';
	}elseif ($typeCom == '远成物流'){
		$typeCom = 'yuanchengwuliu';
	}elseif ($typeCom == '圆通速递'){
		$typeCom = 'yuantong';
	}elseif ($typeCom == '源伟丰快递'){
		$typeCom = 'yuanweifeng';
	}elseif ($typeCom == '元智捷诚快递'){
		$typeCom = 'yuanzhijiecheng';
	}elseif ($typeCom == '越丰物流'){
		$typeCom = 'yuefengwuliu';
	}elseif ($typeCom == '韵达快运'){
		$typeCom = 'yunda';
	}elseif ($typeCom == '源安达'){
		$typeCom = 'yuananda';
	}elseif ($typeCom == '运通快递'){
		$typeCom = 'yuntongkuaidi';
	}elseif ($typeCom == '宅急送'){
		$typeCom = 'zhaijisong';
	}elseif ($typeCom == '中铁快运'){
		$typeCom = 'zhongtiewuliu';
	}elseif ($typeCom == 'EMS快递'){
		$typeCom = 'ems';
	}elseif ($typeCom == '申通快递'){
		$typeCom = 'shentong';
	}elseif ($typeCom == '顺丰速运'){
		$typeCom = 'shunfeng';
	}elseif ($typeCom == '中通速递'){
		$typeCom = 'zhongtong';
	}elseif ($typeCom == '中邮物流'){
		$typeCom = 'zhongyouwuliu';
	} else {
		$typeCom = '';
	}
	return $typeCom;
}

?>