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

$id = _POST('article_id', 0);
if (empty($id)) {
    GZ_Api::outPut(101);
}

if (!$article = get_article_info($id)) {
    GZ_Api::outPut(13);
}

function get_article_info($article_id)
{
    /* 获得文章的信息 */
    $sql = "SELECT a.article_id as id, a.title, a.content ".
            "FROM " .$GLOBALS['ecs']->table('article'). " AS a ".
            "WHERE a.is_open = 1 AND a.article_id = '$article_id'";
    $row = $GLOBALS['db']->getRow($sql);

    return $row;
}

$base = sprintf('<base href="%s/" />', dirname($GLOBALS['ecs']->url()));
$html = '<!DOCTYPE html><html><head><title>'.$article['title'].'</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>img {width: auto\9;height: auto;vertical-align: middle;border: 0;-ms-interpolation-mode: bicubic;max-width: 100%; }html { font-size:100%; } </style>'.$base.'</head><body>'.$article['content'].'</body></html>';

GZ_Api::outPut(array('data' => $html));