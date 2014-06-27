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

$data = array();

$cat_id = _POST('category_id', 0);

$children = get_children($cat_id);
$cat = get_cat_info($cat_id);   // 获得分类的相关信息

if ($cat['grade'] == 0  && $cat['parent_id'] != 0)
{
    $cat['grade'] = get_parent_grade($cat_id); //如果当前分类级别为空，取最近的上级分类
}

if ($cat['grade'] > 1)
{
	$sql = "SELECT min(g.shop_price) AS min, max(g.shop_price) as max ".
           " FROM " . $ecs->table('goods'). " AS g ".
           " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1  ';
           //获得当前分类下商品价格的最大值、最小值

    $row = $db->getRow($sql);

    // 取得价格分级最小单位级数，比如，千元商品最小以100为级数
    $price_grade = 0.0001;
    for($i=-2; $i<= log10($row['max']); $i++)
    {
        $price_grade *= 10;
    }

    //跨度
    $dx = ceil(($row['max'] - $row['min']) / ($cat['grade']) / $price_grade) * $price_grade;
    if($dx == 0)
    {
        $dx = $price_grade;
    }

    for($i = 1; $row['min'] > $dx * $i; $i ++);

    for($j = 1; $row['min'] > $dx * ($i-1) + $price_grade * $j; $j++);
    $row['min'] = $dx * ($i-1) + $price_grade * ($j - 1);

    for(; $row['max'] >= $dx * $i; $i ++);
    $row['max'] = $dx * ($i) + $price_grade * ($j - 1);

    $sql = "SELECT (FLOOR((g.shop_price - $row[min]) / $dx)) AS sn, COUNT(*) AS goods_num  ".
           " FROM " . $ecs->table('goods') . " AS g ".
           " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
           " GROUP BY sn ";

    $price_grade = $db->getAll($sql);

    foreach ($price_grade as $key=>$val)
    {
        $temp_key = $key + 1;
        $price_grade[$temp_key]['goods_num'] = $val['goods_num'];
        $price_grade[$temp_key]['start'] = $row['min'] + round($dx * $val['sn']);
        $price_grade[$temp_key]['end'] = $row['min'] + round($dx * ($val['sn'] + 1));
        $price_grade[$temp_key]['price_range'] = $price_grade[$temp_key]['start'] . '&nbsp;-&nbsp;' . $price_grade[$temp_key]['end'];
        $price_grade[$temp_key]['formated_start'] = price_format($price_grade[$temp_key]['start']);
        $price_grade[$temp_key]['formated_end'] = price_format($price_grade[$temp_key]['end']);
        $price_grade[$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_grade[$temp_key]['start'], 'price_max'=> $price_grade[$temp_key]['end'], 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

        /* 判断价格区间是否被选中 */
        if (isset($_REQUEST['price_min']) && $price_grade[$temp_key]['start'] == $price_min && $price_grade[$temp_key]['end'] == $price_max)
        {
            $price_grade[$temp_key]['selected'] = 1;
        }
        else
        {
            $price_grade[$temp_key]['selected'] = 0;
        }
    }

    // $price_grade[0]['start'] = 0;
    // $price_grade[0]['end'] = 0;
    // $price_grade[0]['price_range'] = $_LANG['all_attribute'];
    // $price_grade[0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
    // $price_grade[0]['selected'] = empty($price_max) ? 1 : 0;
    unset($price_grade[0]);
}

foreach ($price_grade as $key => $val) {
	$data[] = array(
			'price_min' => $val['start'],
			'price_max' => $val['end']
		);
}

GZ_Api::outPut($data);

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('cat_parent_grade');
        if ($data === false)
        {
            $sql = "SELECT parent_id, cat_id, grade ".
                   " FROM " . $GLOBALS['ecs']->table('category');
            $res = $GLOBALS['db']->getAll($sql);
            write_static_cache('cat_parent_grade', $res);
        }
        else
        {
            $res = $data;
        }
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}

?>