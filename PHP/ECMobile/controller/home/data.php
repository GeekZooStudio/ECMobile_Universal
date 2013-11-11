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

require(EC_PATH . '/includes/init.php');

include_once(EC_PATH . '/includes/lib_transaction.php');

$flash_arr = array();

function get_flash_xml()
{
    $flashdb = array();
    if (file_exists(ROOT_PATH . DATA_DIR . '/flash_data.xml'))
    {
        // 兼容v2.7.0及以前版本
        if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER))
        {
            preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER);
        }

        if (!empty($t))
        {
            foreach ($t as $key => $val)
            {
                $val[4] = isset($val[4]) ? $val[4] : 0;
                $val[2] = substr($val[2], 0, 4) == 'http' ? $val[2] : dirname($GLOBALS['ecs']->url()).'/'.$val[2];
                $flashdb[] = array('photo'=>array('small'=>API_DATA('PHOTO', $val[1]), 'thumb'=>API_DATA('PHOTO', $val[1]), 'url'=>API_DATA('PHOTO', $val[1])),'url'=>$val[2],'description'=>$val[3]);
            }
        }
    }
    return $flashdb;
}

$flash_arr['player'] = get_flash_xml();

// url解析
function api_get_url($url) {

    $out = array(
            'action' => '',
            'action_id' => 0
        );

    $site_url = dirname($GLOBALS['ecs']->url());

    if (strpos($url, $site_url) === false) {
        return $out;
    }

    if (strpos($url, '/goods.php') !== false) {
        $action = 'goods';
        $act_arr = explode('/goods.php', $url);
        if (strpos($act_arr[1], '?id=') !== false) {
            $action_id = ltrim($act_arr[1], '?id=');
        }
    } else if (strpos($url, '/category.php') !== false) {
        $action = 'category';
        $act_arr = explode('/category.php', $url);
        if (strpos($act_arr[1], '?id=') !== false) {
            $action_id = ltrim($act_arr[1], '?id=');
        }
    } else if (strpos($url, '/brand.php') !== false) {
        $action = 'brand';
        $act_arr = explode('/brand.php', $url);
        if (strpos($act_arr[1], '?id=') !== false) {
            $action_id = ltrim($act_arr[1], '?id=');
        }
    } else {
        return $out;
    }

    $out['action'] = $action;
    $out['action_id'] = (int)$action_id;

    return $out;
}

foreach ($flash_arr['player'] as $key => $val) {
    $action_info = api_get_url($val['url']);
    $flash_arr['player'][$key]['action'] = $action_info['action'];
    $flash_arr['player'][$key]['action_id'] = $action_info['action_id'];
}

$flash_arr['promote_goods'] = array();
// $best = get_recommend_goods('best');

$sales = gz_get_promote_goods();
if ( count($sales) > 4 ) { $sales4 = array_slice($sales, 0, 4); }
else { $sales4 = $sales; }

if ( !empty($sales4) ) {
	foreach ( $sales4 as $key => $val ) {
		$flash_arr['promote_goods'][] = array(
			'id'=>$val['id'],
			'name'=>$val['name'],
			'market_price'=>$val['market_price'],
			'shop_price'=>$val['shop_price'],
			'promote_price'=>$val['promote_price'],
			'brief'=>$val['brief'],
			'img'=> array(
				'small'=>API_DATA('PHOTO', $val['goods_thumb']),
				'thumb'=>API_DATA('PHOTO', $val['goods_img']),
				'url'=>API_DATA('PHOTO', $val['original_img'])
			)
		);
	}
}

function gz_get_promote_goods($cats = '')
{
    $time = gmtime();
    $order_type = $GLOBALS['_CFG']['recommend_order'];

    /* 取得促销lbi的数量限制 */
    $num = get_library_number("recommend_promotion");
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, g.original_img, b.brand_name, " .
                "g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .
            " AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
    $sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
    $sql .= " LIMIT $num ";
    $result = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($result AS $idx => $row)
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id']           = $row['goods_id'];
        $goods[$idx]['name']         = $row['goods_name'];
        $goods[$idx]['brief']        = $row['goods_brief'];
        $goods[$idx]['brand_name']   = $row['brand_name'];
        $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['goods_thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['original_img']    = get_image_path($row['goods_id'], $row['original_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    return $goods;
}

GZ_Api::outPut($flash_arr);

