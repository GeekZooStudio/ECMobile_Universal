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

function GZ_user_info($user_id)
{
	global $db,$ecs;

	$user_info = user_info($user_id);

	$collection_num = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('collect_goods')." WHERE user_id='$user_id' ORDER BY add_time DESC");
	
	$await_pay = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id'". GZ_order_query_sql('await_pay'));
	$await_ship = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id'". GZ_order_query_sql('await_ship'));
	$shipped = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id'". GZ_order_query_sql('shipped'));
	$finished = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id'". GZ_order_query_sql('finished'));
	
	// include_once(ROOT_PATH .'includes/lib_clips.php');
	// $rank = get_rank_info();
	// print_r($rank);exit;

	/* 取得用户等级 */
	if ($user_info['user_rank'] == 0) {
		// 非特殊等级，根据等级积分计算用户等级（注意：不包括特殊等级）
		$sql = 'SELECT rank_id, rank_name FROM ' . $GLOBALS['ecs']->table('user_rank') . " WHERE special_rank = '0' AND min_points <= " . intval($user_info['rank_points']) . ' AND max_points > ' . intval($user_info['rank_points']);
	} else {
		// 特殊等级
		$sql = 'SELECT rank_id, rank_name FROM ' . $GLOBALS['ecs']->table('user_rank') . " WHERE rank_id = '$user_info[user_rank]'";
	}

	if ($row = $GLOBALS['db']->getRow($sql)) {
        $user_info['user_rank_name']=$row['rank_name'];
    } else {
        $user_info['user_rank_name']='非特殊等级';
    } 

    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('user_rank') . " WHERE special_rank = '0' AND min_points = '0'";
    $row = $GLOBALS['db']->getRow($sql);

    if ($user_info['user_rank_name'] == $row['rank_name']) {
    	$level = 0;
    } else {
    	$level = 1;
    }

	return array(
		'id' => $user_info['user_id'],
		'name'=>$user_info['user_name'],
		'rank_name'=>$user_info['user_rank_name'],
		'rank_level' => $level,
		'collection_num' => $collection_num,
        'email' => $user_info['email'],
		"order_num" => array(
			'await_pay' => $await_pay,
			'await_ship' => $await_ship,
			'shipped' => $shipped,
			'finished' =>$finished
		)
	);
}

/**
 * 生成查询订单的sql
 * @param   string  $type   类型
 * @param   string  $alias  order表的别名（包括.例如 o.）
 * @return  string
 */
function GZ_order_query_sql($type = 'finished', $alias = '')
{
    /* 已完成订单 */
    if ($type == 'finished')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
               " AND {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) .
               " AND {$alias}pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . " ";
    }
    /* 待发货订单 */
    elseif ($type == 'await_ship')
    {
        return " AND   {$alias}order_status " .
                 db_create_in(array(OS_CONFIRMED, OS_SPLITED, OS_SPLITING_PART)) .
               " AND   {$alias}shipping_status " .
                 db_create_in(array(SS_UNSHIPPED, SS_PREPARING, SS_SHIPPED_ING)) .
               " AND ( {$alias}pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . " OR {$alias}pay_id " . db_create_in(payment_id_list(true)) . ") ";
    }
    /* 待付款订单 */
    elseif ($type == 'await_pay')
    {
        return " AND   {$alias}order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED, OS_UNCONFIRMED)) .
               " AND   {$alias}pay_status = '" . PS_UNPAYED . "'" .
               " AND ( {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) . " OR {$alias}pay_id " . db_create_in(payment_id_list(false)) . ") ";
    }
    /* 未确认订单 */
    elseif ($type == 'unconfirmed')
    {
        return " AND {$alias}order_status = '" . OS_UNCONFIRMED . "' ";
    }
    /* 未处理订单：用户可操作 */
    elseif ($type == 'unprocessed')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED)) .
               " AND {$alias}shipping_status = '" . SS_UNSHIPPED . "'" .
               " AND {$alias}pay_status = '" . PS_UNPAYED . "' ";
    }
    /* 未付款未发货订单：管理员可操作 */
    elseif ($type == 'unpay_unship')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED)) .
               " AND {$alias}shipping_status " . db_create_in(array(SS_UNSHIPPED, SS_PREPARING)) .
               " AND {$alias}pay_status = '" . PS_UNPAYED . "' ";
    }
    /* 已发货订单：不论是否付款 */
    elseif ($type == 'shipped')
    {
        return " AND {$alias}shipping_status " . db_create_in(array(SS_SHIPPED)) . " ";
    }
    else
    {
        die('函数 order_query_sql 参数错误');
    }
}



function gz_autoload($className) {
    $file = GZ_PATH . '/Library/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

function getValueByDefault($value, $default) {
    if (!is_array($value)) {
        $whiteList = array();
        if (is_array($default)) {
            $whiteList = $default;
            $default = isset($default[0]) ? $default[0] : $default;
        } elseif ($value == '') {
            return $default;
        }

        if (is_string($default)) {
            $value = trim($value);
        } elseif (is_int($default)) {
            $value = intval($value);
        } elseif (is_array($default)) {
            if ($value == '') {
                return $default;
            }
            $value = (array)$value;
        } else {
            $value = floatval($value);
        }

        if ($whiteList && !in_array($value, $whiteList)) {
            $value = $default;
        }

    } else {
        foreach ($value as $key => $val) {
            $t = isset($default[$key]) ? $default[$key] : '';
            $value[$key] = getValueByDefault($value[$key], $t);
        }
        if (is_array($default)) {
            $value += $default;
        }
    }

    return $value;
}

function _GET($key = '', $default = '') {
    if (empty($key)) {
        return $_GET;
    }

    if (!isset($_GET[$key])) {
        $_GET[$key] = '';
    }
    $value = getValueByDefault($_GET[$key], $default);

    return $value;
}

function _POST($key = '', $default = '') {
    if (empty($key)) {
        return $_POST;
    }

    if (!isset($_POST[$key])) {
        $_POST[$key] = '';
    }
    $value = getValueByDefault($_POST[$key], $default);

    return $value;
}


function getImage($img)
{
    if (substr($img, 0, 4) == 'http') {
        return $img;
    }

    return dirname($GLOBALS['ecs']->url()).'/'.ltrim($img, '/');
}

function formatTime($Time)
{	
	if (strlen($Time) == strlen(intval($Time))) {
		if ($Time == 0) {
			return '';
		}
		$unixTime = $Time;
	} else {
		$unixTime = strtotime($Time);
	}
	return date('Y/m/d H:i:s O', $unixTime);
}

function bjTime($Time)
{
	// $unixTime = $Time + 8*3600;
	// return date('Y/m/d H:i:s O', $unixTime);
	
	return local_date('Y/m/d H:i:s O', $Time);
}

function API_DATA($type, $readData)
{
	$outData = array();
	if (empty($readData)) {
		return $outData;
	}
	if (is_array($readData)) {
		$first = current($readData);
		if ($first && is_array($first)) {
			foreach ($readData as $key => $value) {
				$outData[] = API_DATA($type, $value);
			}
			return array_filter($outData);
		}
	}

	switch ($type) {
		case 'PHOTO':
            $outData = getImage($readData);
			break;
		case 'SIMPLEGOODS':
			$outData = array(
			  "goods_id" => $readData['goods_id'],
			  "name" => $readData['goods_name'],
			  "market_price" => $readData['market_price'],
			  "shop_price" => $readData['shop_price'],
			  "promote_price" => $readData['promote_price'],
			  "img" => array(
				'thumb'=>API_DATA('PHOTO', $readData['goods_img']),
				'url' => API_DATA('PHOTO', $readData['original_img']),
                'small' => API_DATA('PHOTO', $readData['goods_thumb'])
				)
			);
			break;
		case 'ADDRESS':
			$outData = array(
				"id"       => 15,
				"consignee"  => "联系人姓名",
				"email"    => "联系人email",
				"country"  => "国家id",
				"province" => "省id",
				"city"     => "城市id",
				"district" => "地区id",
				"address"  => "详细地址",
				"zipcode"  => "邮政编码",
				"tel"      => "联系电话",
				"mobile"   => "手机",
				"sign_building" => "标志建筑",
				"best_time" => "最佳送货时间"	
			);
			break;
		case 'SIGNUPFIELDS':
			$outData = array(
				"id"  => 12,
			  	"name"  => "说明",
			  	"need"  => 0
			);
			break;
		case 'CONFIG':
			$outData = array(
				"shop_closed" => 0,
			  	"close_comment" => "关闭原因"
			);
			break;
		case 'CATEGORY':
			$outData = array(
				"id"    => 12,
			  	"name"  => "分类名称",
			  	"children"  => array(
			  		'id'   =>  13,
					'name' => 'ssss'
			  	)
			);
			break;
		case 'SIMPLEORDER':
			$outData = array(
			  "id" => $readData['order_id'],
			  "order_sn" => $readData['order_sn'],
			  "order_time" => $readData['order_time'],
			  "order_status" => $readData['order_status'],
			  "total_fee" => $readData['total_fee'],
			);
			break;
		case 'GOODS':
            $readData['original_img'] || $readData['original_img'] = $readData['goods_thumb'];
			$outData = array(
				"id"  =>  $readData['goods_id'],
				"cat_id" => $readData['cat_id'],
				"goods_sn" => $readData['goods_sn'],
				"goods_name" => $readData['goods_name'],
				// "goods_desc"=>$readData['goods_desc'],
                "collected" => $readData['collected'],
				"market_price" => $readData['market_price'],
				"shop_price" => price_format($readData['shop_price'], false),
				"integral" => $readData['integral'],
				"click_count" => $readData['click_count'],
				"brand_id" => $readData['brand_id'],
                // fix 没有goods_number值
				"goods_number" => is_numeric($readData['goods_number']) ? $readData['goods_number'] : 65535,
				"goods_weight" =>  $readData['goods_weight'],
				"promote_price" => $readData['promote_price_org'],
				"formated_promote_price" => price_format($readData['promote_price_org'], false),//$readData['promote_price'],
				"promote_start_date" => bjTime($readData['promote_start_date']),
				"promote_end_date"  => bjTime($readData['promote_end_date']),
				"is_shipping" => $readData['is_shipping'],
				"img" => array(
					'thumb'=>API_DATA('PHOTO', $readData['goods_img']),
					'url' => API_DATA('PHOTO', $readData['original_img']),
					'small'=>API_DATA('PHOTO', $readData['goods_thumb'])
				 ),
				"rank_prices" => array(),
				"pictures" => array(),
				"properties" => array(),
				"specification" => array()
			);
			foreach ($readData['rank_prices'] as $key => $value) {
				$outData['rank_prices'][] = array(
					"id"   =>  $key,
					"rank_name" => $value['rank_name'],
					"price" => $value['price']
				);
			}

			foreach ($readData['pictures'] as $key => $value) {
				$outData['pictures'][] = array(
					"small"=>API_DATA('PHOTO', $value['thumb_url']),
					"thumb"=>API_DATA('PHOTO', $value['thumb_url']),
					"url"=>API_DATA('PHOTO', $value['img_url'])
				);
			}

            if (!empty($readData['properties'])) {
                // $readData['properties'] = current($readData['properties']);
    			foreach ($readData['properties'] as $key => $value) {
                    // 处理分组
                    foreach ($value as $k => $v) {
                        $v['value'] = strip_tags($v['value']);
        				$outData['properties'][] = $v;
                    }
    			}
            }

			foreach ($readData['specification'] as $key => $value) {
				if (!empty($value['values'])) {
					$value['value'] = $value['values'];
					unset($value['values']);	
				}
				$outData['specification'][] = $value;
			}
			break;
        default:
            break;
    }
    return $outData;
}
