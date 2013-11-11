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

class goodsTest extends PHPUnit_Framework_TestCase
{
    function testOne()
    {
        $data = array(
            'pagination' => array('page' => 1, 'count' => 1)
        );
        $out = request_by_curl('search', $data);
        $item = $out['data'][0];

        $data = array(
            'goods_id' => $item['goods_id']
        );
    
        $out = request_by_curl('goods', $data);
        $this->assertArrayHasKey('pictures', $out['data']);
        $this->assertArrayHasKey('properties', $out['data']);
        $this->assertArrayHasKey('specification', $out['data']);
        //return $out;
    }
}