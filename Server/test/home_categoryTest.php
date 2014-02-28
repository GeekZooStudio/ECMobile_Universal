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

class home_categoryTest extends PHPUnit_Framework_TestCase
{
    function testOne()
    {
        $data = array();
        $out = request_by_curl('home/category', $data);
        $this->assertEquals(1, $out['status']['succeed']);
        foreach ($out['data'] as $val) {
            $this->assertArrayHasKey('goods', $val);
            foreach ($val['goods'] as $k) {
                $diff = array('id', 'name', 'market_price', 'shop_price', 'promote_price', 'brief', 'img');
                $keys = array_keys($k);
                $this->assertEmpty(array_diff($diff, $keys));
            }
        }
    }
}