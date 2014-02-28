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

include_once(EC_PATH . '/includes/lib_order.php');
/* 载入语言文件 */
require_once(EC_PATH . '/languages/' .$_CFG['lang']. '/user.php');
require_once(EC_PATH . '/languages/' .$_CFG['lang']. '/shopping_flow.php');

if (empty($tmp[0])) {
	GZ_Api::outPut(101);
}

switch ($tmp[0]) {
	case 'bonus':
		   $bonus_sn = trim(_POST('bonus_sn'));
		    if (is_numeric($bonus_sn))
		    {
		        $bonus = bonus_info(0, $bonus_sn);
		    }
		    else
		    {
		        $bonus = array();
		    }

		    $bonus_kill = price_format($bonus['type_money'], false);

		    $result = array('error' => '', 'content' => '');

		    /* 取得购物类型 */
		    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

		    /* 获得收货人信息 */
		    $consignee = get_consignee($_SESSION['user_id']);

		    /* 对商品信息赋值 */
		    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

		    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
		    {
		        $result['error'] = $_LANG['no_goods_in_cart'];
		    }
		    else
		    {
		        /* 取得购物流程设置 */
		        $smarty->assign('config', $_CFG);

		        /* 取得订单信息 */
		        $order = flow_order_info();


		        if (((!empty($bonus) && $bonus['user_id'] == $_SESSION['user_id']) || ($bonus['type_money'] > 0 && empty($bonus['user_id']))) && $bonus['order_id'] <= 0)
		        {
		            //$order['bonus_kill'] = $bonus['type_money'];
		            $now = gmtime();
		            if ($now > $bonus['use_end_date'])
		            {
		                $order['bonus_id'] = '';
		                $result['error']=$_LANG['bonus_use_expire'];
		            }
		            else
		            {
		                $order['bonus_id'] = $bonus['bonus_id'];
		                $order['bonus_sn'] = $bonus_sn;
		            }
		        }
		        else
		        {
		            //$order['bonus_kill'] = 0;
		            $order['bonus_id'] = '';
		            $result['error'] = $_LANG['invalid_bonus'];
		        }

		        /* 计算订单的费用 */
		        $total = order_fee($order, $cart_goods, $consignee);

		        if($total['goods_price']<$bonus['min_goods_amount'])
		        {
		         $order['bonus_id'] = '';
		         /* 重新计算订单 */
		         $total = order_fee($order, $cart_goods, $consignee);
		         $result['error'] = sprintf($_LANG['bonus_min_amount_error'], price_format($bonus['min_goods_amount'], false));
		        }

		        $smarty->assign('total', $total);

		        /* 团购标志 */
		        if ($flow_type == CART_GROUP_BUY_GOODS)
		        {
		            $smarty->assign('is_group_buy', 1);
		        }

		        $result['is_group_buy'] = $smarty->_var['is_group_buy'];
		        // $result['config'] = $smarty->_var['config'];
		        $result['total'] = $smarty->_var['total'];
		    }
			if (!empty($result['error'])) {
				GZ_Api::outPut(101);
			}
			// print_r($result);exit;
			$out = array('bonus'=>$result['total']['bonus'], 'bonus_formated'=>$result['total']['bonus_formated']);
			GZ_Api::outPut($out);
		break;
	   case 'integral':
			$integral = _POST('integral', 0);
			if (!$integral) {
				GZ_Api::outPut(101);
			}
	   	 	// $user_id = $_SESSION['user_id'];
	   	 	// 	        $user_info = user_info($user_id);
	   	 	// 	        // 查询用户有多少积分
	   	 	// 	        $flow_points = flow_available_points();  // 该订单允许使用的积分
	        // $user_points = $user_info['pay_points']; // 用户的积分总数
			$integral_to_p =  value_of_integral($integral);
			// print_r($user_info);
			// print_r($flow_points);exit;
			GZ_Api::outPut(array(
				"bonus" => $integral_to_p,
		        "bonus_formated" => price_format($integral_to_p)
			));
	   	break;
}

GZ_Api::outPut(101);

/**
 * 获得用户的可用积分
 *
 * @access  private
 * @return  integral
 */
function flow_available_points()
{
    $sql = "SELECT SUM(g.integral * c.goods_number) ".
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.session_id = '" . SESS_ID . "' AND c.goods_id = g.goods_id AND c.is_gift = 0 AND g.integral > 0 " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "'";

    $val = intval($GLOBALS['db']->getOne($sql));

    return integral_of_value($val);
}

?>