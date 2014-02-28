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
// define('DEBUG_MODE', 1);
// define('INIT_NO_SMARTY', true);
// $smarty = new GZ_Smarty('search');

$data = array();

$cat_id = _POST('category_id', 0);

if (!empty($cat_id)) {
	$children = get_children($cat_id);
	$sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(*) AS goods_num ".
	            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ". $GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
	            "WHERE g.brand_id = b.brand_id AND ($children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), array_keys(cat_list($cat_id, 0, false))))) . ") AND b.is_show = 1 " .
	            " AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
	            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";

	$brand_list = $GLOBALS['db']->getAll($sql);
} else {
	$brand_list = get_brands();
}

foreach ($brand_list as $key => $val) {
    $data[] = array(
          'url' => $val['brand_logo'],
          'brand_name' => $val['brand_name'],
          'brand_id' => $val['brand_id']
      );
}

GZ_Api::outPut($data);

?>