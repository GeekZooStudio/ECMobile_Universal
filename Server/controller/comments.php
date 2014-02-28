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
$goods_id = _POST('goods_id', 0);
if (!$goods_id) {
	GZ_Api::outPut(101);
}
$page_size = GZ_Api::$pagination['count'];
$page = GZ_Api::$pagination['page'];
//0评论的是商品,1评论的是文章
$out = GZ_assign_comment($goods_id, 0, $page, $page_size);
GZ_Api::outPut($out['comments'], $out['pager']);

/**
 * 查询评论内容
 *
 * @access  public
 * @params  integer     $id
 * @params  integer     $type
 * @params  integer     $page
 * @return  array
 */
function GZ_assign_comment($id, $type, $page = 1, $page_size = 10)
{
    /* 取得评论列表 */
    $count = $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('comment').
           " WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0");

    $page_count = ($count > 0) ? intval(ceil($count / $page_size)) : 1;

    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('comment') .
            " WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0".
            ' ORDER BY comment_id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $page_size, ($page-1) * $page_size);

    $arr = array();
    $ids = '';
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ids .= $ids ? ",$row[comment_id]" : $row['comment_id'];
        $arr[$row['comment_id']]['id']       = $row['comment_id'];
        // $arr[$row['comment_id']]['email']    = $row['email'];
        $arr[$row['comment_id']]['author'] = empty($row['user_name']) ? '匿名用户' : $row['user_name'] ;
        $arr[$row['comment_id']]['content']  = str_replace('\r\n', '<br />', htmlspecialchars($row['content']));
        $arr[$row['comment_id']]['content']  = nl2br(str_replace('\n', '<br />', $arr[$row['comment_id']]['content']));
        // $arr[$row['comment_id']]['rank']     = $row['comment_rank'];
        $arr[$row['comment_id']]['create'] = formatTime($row['add_time']);
        $arr[$row['comment_id']]['re_content'] = '';
    }
    /* 取得已有回复的评论 */
    if ($ids)
    {
        $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('comment') .
                " WHERE parent_id IN( $ids )";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetch_array($res))
        {
            $arr[$row['parent_id']]['re_content']  = nl2br(str_replace('\n', '<br />', htmlspecialchars($row['content'])));
            // $arr[$row['parent_id']]['re_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
            // $arr[$row['parent_id']]['re_email']    = $row['email'];
            // $arr[$row['parent_id']]['re_username'] = $row['user_name'];
        }
    }

	
	$pager = array(
			"total"  => $count,	 
			"count"  => count($arr),
			"more"   => $page < $page_count ? 1 : 0
	);

    $cmt = array('comments' => array_values($arr), 'pager' => $pager);

    return $cmt;
}

