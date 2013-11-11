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

abstract class GZ_Api
{
    public static $session = array();
    public static $pagination = array();

    protected static $error = array(
        6   => '密码错误',
		8 	=>	'处理失败',
		11  => '用户名或email已使用',
		13  => '不存在的信息',
		14	=>	'购买失败',
        100 => 'Invalid session',
        101 => '错误的参数提交',
        501 => '没有pagination结构',
        502 => 'code错误',
        503 => '合同期终止',
		10001=>'您必须选定一个配送方式',
		10002=>'购物车中没有商品',
		10003=>'您的余额不足以支付整个订单，请选择其他支付方式',
		10005=>'您选择的超值礼包数量已经超出库存。请您减少购买量或联系商家。',
		10006=>'如果是团购，且保证金大于0，不能使用货到付款',
		10007=>'您已收藏过此商品',
		10008=>'库存不足',
		10009=>'订单无发货信息'
    );


    public static function init()
    {
        if (!empty($_POST['json'])) {
			if (get_magic_quotes_gpc()) {
				$_POST['json'] = stripslashes($_POST['json']);
			}
            $_POST = json_decode($_POST['json'], true);
        }
        self::$session = _POST('session', array());

        self::$pagination = _POST('pagination', array('page' => 1, 'count' => 10));
    }

    public static function authSession()
    {
        if (!isset(self::$session['uid']) || !isset(self::$session['sid'])) {
            self::outPut(100);
        }

	    /* 初始化session */
	    include(EC_PATH . '/includes/cls_session.php');

	    $sess = new GZ_session($GLOBALS['db'], $GLOBALS['ecs']->table('sessions'), $GLOBALS['ecs']->table('sessions_data'), 'ECS_ID', self::$session['sid']);

	    define('SESS_ID', $sess->get_session_id());
		
		if (empty($_SESSION['user_id'])) {
            self::outPut(100);
		}
    }

    public static function outPut($data, $pager = NULL)
    {
        if (!is_array($data)) {
            $status = array(
                'status' => array(
                    'succeed' => 0,
                    'error_code' => $data,
                    'error_desc' => self::$error[$data]
                )
            );
            die(json_encode($status));
        }
		if (isset($data['data'])) {
		    $data = $data['data'];
		}
        $data = array_merge(array('data'=>$data), array('status' => array('succeed' => 1)));
		if (!empty($pager)) {
			$data = array_merge($data, array('paginated'=>$pager));
		}
        die(json_encode($data));
    }
}