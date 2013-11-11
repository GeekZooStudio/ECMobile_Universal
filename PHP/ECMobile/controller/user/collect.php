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

GZ_Api::authSession();

if (empty($tmp[0])) {
	GZ_Api::outPut(101);
}

switch ($tmp[0]) {
	case 'create':
	    $goods_id = _POST('goods_id', 0);
		if (!$goods_id) {
			GZ_Api::outPut(101);
		}
		$goods = get_goods_info($goods_id);
	    if (!$goods) {
			GZ_Api::outPut(13);
	    }
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('collect_goods') .
            " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0)
        {
			GZ_Api::outPut(10007);
        } else {
            $time = gmtime();
            $sql = "INSERT INTO " .$GLOBALS['ecs']->table('collect_goods'). " (user_id, goods_id, add_time)" .
                    "VALUES ('$_SESSION[user_id]', '$goods_id', '$time')";

            if ($GLOBALS['db']->query($sql) === false)
            {
                GZ_Api::outPut(8);
            } else {
                GZ_Api::outPut(array());
            }
        }
		break;
	case 'delete':
		include_once(EC_PATH . '/includes/lib_clips.php');

	    $collection_id = _POST('rec_id', 0);
		if (!$collection_id) {
			GZ_Api::outPut(101);
		}
	    if ($collection_id > 0)
	    {
	        $db->query('DELETE FROM ' .$ecs->table('collect_goods'). " WHERE rec_id='$collection_id' AND user_id =".$_SESSION['user_id'] );
	    }
	    GZ_Api::outPut(array());
		break;
	case 'list':
		include_once(EC_PATH . '/includes/lib_clips.php');
		$page = GZ_Api::$pagination;
		$user_id = $_SESSION['user_id'];
	    $rec_id = _POST('rec_id', 0);

        $add = '';
        if ($rec_id) {
            $add = " AND rec_id < $rec_id ";
        }

	    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('collect_goods').
	                                " WHERE user_id='$user_id' $add");

	    $smarty->assign('goods_list', GZ_get_collection_goods($user_id, $page['count'], $page['page'], $rec_id));
	    $smarty->assign('url',        $ecs->url());
	    $smarty->assign('user_id',  $user_id);
		$data = array();
		foreach ($smarty->_var['goods_list'] as $key => $value) {
			$temp = API_DATA("SIMPLEGOODS", $value);
			$temp['rec_id'] = $value['rec_id'];
			$data[] = $temp;
		}


	    $pager = get_pager('collection', array(), $record_count, $page['page'], $page['count']);


  		GZ_Api::outPut($data, array(
			"total"  => $record_count,	 
			"count"  => count($data),
			"more"   => intval($pager['page_count'] > $pager['page'])
  		));
		break;
	default:
		GZ_Api::outPut(101);
		break;
}

/**
 *  获取指定用户的收藏商品列表
 *
 * @access  public
 * @param   int     $user_id        用户ID
 * @param   int     $num            列表最大数量
 * @param   int     $start          列表其实位置
 *
 * @return  array   $arr
 */
function GZ_get_collection_goods($user_id, $num = 10, $start = 1, $rec_id = 0)
{
    $add = '';
    if ($rec_id) {
        $add = " AND c.rec_id < $rec_id ";
    }
    $sql = 'SELECT g.original_img,g.goods_id, g.goods_name, g.market_price, g.shop_price, g.goods_thumb, g.goods_img, g.original_img, g.goods_brief, g.goods_type AS org_price, '.
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.promote_price, g.promote_start_date,g.promote_end_date, c.rec_id, c.is_attention' .
            ' FROM ' . $GLOBALS['ecs']->table('collect_goods') . ' AS c' .
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ".
                "ON g.goods_id = c.goods_id ".
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            " WHERE c.user_id = '$user_id' $add ORDER BY c.rec_id DESC";
    // $res = $GLOBALS['db'] -> selectLimit($sql, $num, $start);
    $res = $GLOBALS['db']->selectLimit($sql, $num, ($start - 1) * $num);
	
    $goods_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        $goods_list[$row['goods_id']]['rec_id']        = $row['rec_id'];
        $goods_list[$row['goods_id']]['is_attention']  = $row['is_attention'];
        $goods_list[$row['goods_id']]['goods_id']      = $row['goods_id'];
        $goods_list[$row['goods_id']]['goods_name']    = $row['goods_name'];
        $goods_list[$row['goods_id']]['market_price']  = price_format($row['market_price']);
        $goods_list[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $goods_list[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $goods_list[$row['goods_id']]['url']           = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $goods_list[$row['goods_id']]['original_img']           = $row['original_img'];
        $goods_list[$row['goods_id']]['goods_thumb']           = $row['goods_thumb'];
        $goods_list[$row['goods_id']]['goods_brief']           = $row['goods_brief'];
        $goods_list[$row['goods_id']]['goods_type']           = $row['goods_type'];
        $goods_list[$row['goods_id']]['goods_img']           = $row['goods_img'];
    }
    return $goods_list;
}
