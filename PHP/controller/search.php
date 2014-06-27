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

$filter = _POST('filter', array());

// print_r($filter);exit;

$brand_id = $filter['brand_id'];

$price_range['price_min'] = !empty($filter['price_range']['price_min']) ? $filter['price_range']['price_min'] : 0;
$price_range['price_max'] = !empty($filter['price_range']['price_max']) ? $filter['price_range']['price_max'] : 0;

// $price_range = $filter['price_range'];

// $price_range['price_min'] = 300;
// $price_range['price_max'] = 5000;

$keyword = $filter['keywords'];
$category = $filter['category_id'];
$sort_by = $filter['sort_by'];
$order = 'DESC';
$sort = 'goods_id';
$intro = '';
// print_r($price_range);exit;
if ($sort_by == 'is_hot') {
    $sort = 'is_hot DESC, goods_id';
    $order = 'DESC';
    //$intro = 'hot';
} elseif ($sort_by == 'price_desc') {
    $sort = 'shop_price';
} elseif ($sort_by == 'price_asc') {
    $sort = 'shop_price';
    $order = 'ASC';
}

// print_r(GZ_Api::$pagination);exit;
// "sort_by"  : "price_desc|price_asc|is_hot"

// var_dump($price_range);exit;
$_REQUEST = array();

$_REQUEST['keywords'] = $keyword;
$_REQUEST['category'] = $category;
$_REQUEST['brand'] = $brand_id;
$_REQUEST['min_price'] = $price_range['price_min'];
$_REQUEST['max_price'] = $price_range['price_max'];
$_REQUEST['goods_type'] = 0;
$_REQUEST['intro'] = $intro;

$_REQUEST['order'] = $order;
$_REQUEST['sort'] = $sort;
$_REQUEST['page_size'] = GZ_Api::$pagination['count'];
$_REQUEST['page'] = GZ_Api::$pagination['page'];
// print_r($_REQUEST);exit;
// if (get_magic_quotes_gpc())
// {
//     require(EC_PATH.'/includes/lib_base.php');
// 
//     $string = stripslashes_deep($string);
// }
// $string['search_encode_time'] = time();
// $string = str_replace('+', '%2b', base64_encode(serialize($string)));
// $_GET['encode'] = $string;
// include(EC_PATH.'/search.php');
// exit;
$_REQUEST['keywords']   = !empty($_REQUEST['keywords'])   ? htmlspecialchars(trim($_REQUEST['keywords']))     : '';
  $_REQUEST['brand']      = !empty($_REQUEST['brand'])      ? intval($_REQUEST['brand'])      : 0;
  $_REQUEST['category']   = !empty($_REQUEST['category'])   ? intval($_REQUEST['category'])   : 0;
  $_REQUEST['min_price']  = !empty($_REQUEST['min_price'])  ? intval($_REQUEST['min_price'])  : 0;
  $_REQUEST['max_price']  = !empty($_REQUEST['max_price'])  ? intval($_REQUEST['max_price'])  : 0;
  $_REQUEST['goods_type'] = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
  $_REQUEST['sc_ds']      = !empty($_REQUEST['sc_ds']) ? intval($_REQUEST['sc_ds']) : 0;
  $_REQUEST['outstock']   = !empty($_REQUEST['outstock']) ? 1 : 0;


  /* 初始化搜索条件 */
  $keywords  = '';
  $tag_where = '';
  if (!empty($_REQUEST['keywords']))
  {
      $arr = array();
      if (stristr($_REQUEST['keywords'], ' AND ') !== false)
      {
          /* 检查关键字中是否有AND，如果存在就是并 */
          $arr        = explode('AND', $_REQUEST['keywords']);
          $operator   = " AND ";
      }
      elseif (stristr($_REQUEST['keywords'], ' OR ') !== false)
      {
          /* 检查关键字中是否有OR，如果存在就是或 */
          $arr        = explode('OR', $_REQUEST['keywords']);
          $operator   = " OR ";
      }
      elseif (stristr($_REQUEST['keywords'], ' + ') !== false)
      {
          /* 检查关键字中是否有加号，如果存在就是或 */
          $arr        = explode('+', $_REQUEST['keywords']);
          $operator   = " OR ";
      }
      else
      {
          /* 检查关键字中是否有空格，如果存在就是并 */
          $arr        = explode(' ', $_REQUEST['keywords']);
          $operator   = " AND ";
      }

      $keywords = 'AND (';
      $goods_ids = array();
      foreach ($arr AS $key => $val)
      {
          if ($key > 0 && $key < count($arr) && count($arr) > 1)
          {
              $keywords .= $operator;
          }
          $val        = mysql_like_quote(trim($val));
          $sc_dsad    = $_REQUEST['sc_ds'] ? " OR goods_desc LIKE '%$val%'" : '';
          $keywords  .= "(goods_name LIKE '%$val%' OR goods_sn LIKE '%$val%' OR keywords LIKE '%$val%' $sc_dsad)";

          $sql = 'SELECT DISTINCT goods_id FROM ' . $ecs->table('tag') . " WHERE tag_words LIKE '%$val%' ";
          $res = $db->query($sql);
          while ($row = $db->FetchRow($res))
          {
              $goods_ids[] = $row['goods_id'];
          }

          $db->autoReplace($ecs->table('keywords'), array('date' => local_date('Y-m-d'),
              'searchengine' => 'ecshop', 'keyword' => addslashes(str_replace('%', '', $val)), 'count' => 1), array('count' => 1));
      }
      $keywords .= ')';

      $goods_ids = array_unique($goods_ids);
      $tag_where = implode(',', $goods_ids);
      if (!empty($tag_where))
      {
          $tag_where = 'OR g.goods_id ' . db_create_in($tag_where);
      }
  }

  $category   = !empty($_REQUEST['category']) ? intval($_REQUEST['category'])        : 0;
  $categories = ($category > 0)               ? ' AND ' . get_children($category)    : '';
  // $brand      = $_REQUEST['brand']            ? " AND brand_id = '$_REQUEST[brand]'" : '';
  $brand      = $_REQUEST['brand']            ? " AND brand_id = '".$_REQUEST['brand']."'" : '';
  $outstock   = !empty($_REQUEST['outstock']) ? " AND g.goods_number > 0 "           : '';

  // $min_price  = $_REQUEST['min_price'] != 0                               ? " AND g.shop_price >= '$_REQUEST[min_price]'" : '';
  // $max_price  = $_REQUEST['max_price'] != 0 || $_REQUEST['min_price'] < 0 ? " AND g.shop_price <= '$_REQUEST[max_price]'" : '';
  $min_price  = $_REQUEST['min_price'] != 0                               ? " AND g.shop_price >= '".$_REQUEST['min_price']."'" : '';
  $max_price  = $_REQUEST['max_price'] != 0 || $_REQUEST['min_price'] < 0 ? " AND g.shop_price <= '".$_REQUEST['max_price']."'" : '';

  /* 排序、显示方式以及类型 */
  $default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
  $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
  $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

  $sort = isset($_REQUEST['sort']) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
  $order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
  $display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_SESSION['display_search']) ? $_SESSION['display_search'] : $default_display_type);

  $_SESSION['display_search'] = $display;

  $page       = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
  $size       = !empty($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0 ? intval($_REQUEST['page_size']) : 10;

  $intromode = '';    //方式，用于决定搜索结果页标题图片

  if (!empty($_REQUEST['intro']))
  {
      switch ($_REQUEST['intro'])
      {
          case 'best':
              $intro   = ' AND g.is_best = 1';
              $intromode = 'best';
              $ur_here = $_LANG['best_goods'];
              break;
          case 'new':
              $intro   = ' AND g.is_new = 1';
              $intromode ='new';
              $ur_here = $_LANG['new_goods'];
              break;
          case 'hot':
              $intro   = ' AND g.is_hot = 1';
              $intromode = 'hot';
              $ur_here = $_LANG['hot_goods'];
              break;
          case 'promotion':
              $time    = gmtime();
              $intro   = " AND g.promote_price > 0 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time'";
              $intromode = 'promotion';
              $ur_here = $_LANG['promotion_goods'];
              break;
          default:
              $intro   = '';
      }
  }
  else
  {
      $intro = '';
  }

  if (empty($ur_here))
  {
      $ur_here = $_LANG['search_goods'];
  }

  /*------------------------------------------------------ */
  //-- 属性检索
  /*------------------------------------------------------ */
  $attr_in  = '';
  $attr_num = 0;
  $attr_url = '';
  $attr_arg = array();

  if (!empty($_REQUEST['attr']))
  {
      $sql = "SELECT goods_id, COUNT(*) AS num FROM " . $ecs->table("goods_attr") . " WHERE 0 ";
      foreach ($_REQUEST['attr'] AS $key => $val)
      {
          if (is_not_null($val) && is_numeric($key))
          {
              $attr_num++;
              $sql .= " OR (1 ";

              if (is_array($val))
              {
                  $sql .= " AND attr_id = '$key'";

                  if (!empty($val['from']))
                  {
                      $sql .= is_numeric($val['from']) ? " AND attr_value >= " . floatval($val['from'])  : " AND attr_value >= '$val[from]'";
                      $attr_arg["attr[$key][from]"] = $val['from'];
                      $attr_url .= "&amp;attr[$key][from]=$val[from]";
                  }

                  if (!empty($val['to']))
                  {
                      $sql .= is_numeric($val['to']) ? " AND attr_value <= " . floatval($val['to']) : " AND attr_value <= '$val[to]'";
                      $attr_arg["attr[$key][to]"] = $val['to'];
                      $attr_url .= "&amp;attr[$key][to]=$val[to]";
                  }
              }
              else
              {
                  /* 处理选购中心过来的链接 */
                  $sql .= isset($_REQUEST['pickout']) ? " AND attr_id = '$key' AND attr_value = '" . $val . "' " : " AND attr_id = '$key' AND attr_value LIKE '%" . mysql_like_quote($val) . "%' ";
                  $attr_url .= "&amp;attr[$key]=$val";
                  $attr_arg["attr[$key]"] = $val;
              }

              $sql .= ')';
          }
      }

      /* 如果检索条件都是无效的，就不用检索 */
      if ($attr_num > 0)
      {
          $sql .= " GROUP BY goods_id HAVING num = '$attr_num'";

          $row = $db->getCol($sql);
          if (count($row))
          {
              $attr_in = " AND " . db_create_in($row, 'g.goods_id');
          }
          else
          {
              $attr_in = " AND 0 ";
          }
      }
  }
  elseif (isset($_REQUEST['pickout']))
  {
      /* 从选购中心进入的链接 */
      $sql = "SELECT DISTINCT(goods_id) FROM " . $ecs->table('goods_attr');
      $col = $db->getCol($sql);
      //如果商店没有设置商品属性,那么此检索条件是无效的
      if (!empty($col))
      {
          $attr_in = " AND " . db_create_in($col, 'g.goods_id');
      }
  }

  /* 获得符合条件的商品总数 */
  $sql   = "SELECT COUNT(*) FROM " .$ecs->table('goods'). " AS g ".
      "WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 $attr_in ".
      "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock ." ) ".$tag_where." )";
  $count = $db->getOne($sql);

  $max_page = ($count> 0) ? ceil($count / $size) : 1;
  if ($page > $max_page)
  {
      $page = $max_page;
  }

  /* 查询商品 */
  $sql = "SELECT g.goods_id, g.goods_name, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ".
              "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
              "g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_thumb, g.goods_img, g.original_img, g.goods_brief, g.goods_type ".
          "FROM " .$ecs->table('goods'). " AS g ".
          "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                  "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
          "WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 $attr_in ".
              "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock . " ) ".$tag_where." ) " .
          "ORDER BY $sort $order";
  $res = $db->SelectLimit($sql, $size, ($page - 1) * $size);

  $arr = array();
  while ($row = $db->FetchRow($res))
  {
      if ($row['promote_price'] > 0)
      {
          $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
      }
      else
      {
          $promote_price = 0;
      }

      /* 处理商品水印图片 */
      /* 处理商品水印图片 */
      $watermark_img = '';

      if ($promote_price != 0)
      {
          $watermark_img = "watermark_promote_small";
      }
      elseif ($row['is_new'] != 0)
      {
          $watermark_img = "watermark_new_small";
      }
      elseif ($row['is_best'] != 0)
      {
          $watermark_img = "watermark_best_small";
      }
      elseif ($row['is_hot'] != 0)
      {
          $watermark_img = 'watermark_hot_small';
      }

      if ($watermark_img != '')
      {
          $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
      }

      $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
      $arr[$row['goods_id']]['goods_name'] = $row['goods_name'];

      $arr[$row['goods_id']]['type']          = $row['goods_type'];
      $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
      $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
      $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';

      $arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
      $arr[$row['goods_id']]['original_img']   = get_image_path($row['goods_id'], $row['original_img'], true);
      $arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
      $arr[$row['goods_id']]['goods_thumb']     = get_image_path($row['goods_id'], $row['goods_thumb']);
      $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
  }

  $smarty->assign('goods_list', $arr);
  $smarty->assign('category',   $category);
  $smarty->assign('keywords',   htmlspecialchars(stripslashes($_REQUEST['keywords'])));
  $smarty->assign('search_keywords',   stripslashes(htmlspecialchars_decode($_REQUEST['keywords'])));
  $smarty->assign('brand',      $brand);
  $smarty->assign('min_price',  $min_price);
  $smarty->assign('max_price',  $max_price);
  $smarty->assign('outstock',  $_REQUEST['outstock']);

  /* 分页 */
  $url_format = "search.php?category=$category&amp;keywords=" . urlencode(stripslashes($_REQUEST['keywords'])) . "&amp;brand=" . $brand."&amp;action=".$action."&amp;goods_type=" . $_REQUEST['goods_type'] . "&amp;sc_ds=" . $_REQUEST['sc_ds'];
  if (!empty($intromode))
  {
      $url_format .= "&amp;intro=" . $intromode;
  }
  if (isset($_REQUEST['pickout']))
  {
      $url_format .= '&amp;pickout=1';
  }
  $url_format .= "&amp;min_price=" . $_REQUEST['min_price'] ."&amp;max_price=" . $_REQUEST['max_price'] . "&amp;sort=$sort";

  $url_format .= "$attr_url&amp;order=$order&amp;page=";

  $pager['search'] = array(
      'keywords'   => stripslashes(urlencode($_REQUEST['keywords'])),
      'category'   => $category,
      'brand'      => $brand,
      'sort'       => $sort,
      'order'      => $order,
      'min_price'  => $_REQUEST['min_price'],
      'max_price'  => $_REQUEST['max_price'],
      'action'     => $action,
      'intro'      => empty($intromode) ? '' : trim($intromode),
      'goods_type' => $_REQUEST['goods_type'],
      'sc_ds'      => $_REQUEST['sc_ds'],
      'outstock'   => $_REQUEST['outstock']
  );
  $pager['search'] = array_merge($pager['search'], $attr_arg);

  $pager = get_pager('search.php', $pager['search'], $count, $page, $size);
  $pager['display'] = $display;

  $smarty->assign('url_format', $url_format);
  $smarty->assign('pager', $pager);

  assign_template();
  assign_dynamic('search');
  $position = assign_ur_here(0, $ur_here . ($_REQUEST['keywords'] ? '_' . $_REQUEST['keywords'] : ''));
  $smarty->assign('page_title', $position['title']);    // 页面标题
  $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
  $smarty->assign('intromode',      $intromode);
  $smarty->assign('categories', get_categories_tree()); // 分类树
  $smarty->assign('helps',       get_shop_help());      // 网店帮助
  $smarty->assign('top_goods',  get_top10());           // 销售排行
  $smarty->assign('promotion_info', get_promotion_info());
  $data = API_DATA("SIMPLEGOODS", $smarty->_var['goods_list']);

    if (!empty($smarty->_var['pager'])) {
    	$pager = array(
    			"total"  => $smarty->_var['pager']['record_count'],	 
    			"count"  => count($smarty->_var['goods_list']),
    			"more"   => empty($smarty->_var['pager']['page_next']) ? 0 : 1
    	);
    } else {
    	$pager = NULL;
    }
GZ_Api::outPut($data, $pager);

?>