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

class articleTest extends PHPUnit_Framework_TestCase
{
    function testeveryArticleFromHelpCategory()
    {
        $data = array();
        $out = request_by_curl('shopHelp', $data);
        foreach ($out['data'] as $key => $value) {
            //保证每一个文章可访问
            foreach ($value['article'] as $v) {
                $data = array(
                    'article_id' => $v['id']
                );
                $out = request_by_curl('article', $data);
                $this->assertEquals(1, $out['status']['succeed']);
            }
        }
    }
}