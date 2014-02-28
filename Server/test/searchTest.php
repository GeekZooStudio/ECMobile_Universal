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

class searchTest extends PHPUnit_Framework_TestCase
{
    function testOne()
    {
        $data = array(
            'pagination' => array('page' => 1, 'count' => 100)
        );
        $out = request_by_curl('search', $data);
        $this->assertTrue(count($out['data']) == $out['paginated']['count']);

        return $out;
    }
    
    function testTwo()
    {
        $data = array(
            'pagination' => array('page' => 1, 'count' => 2),
            'category_id' => 1,
            'sort_by' => 'price_desc'
        );
        $out = request_by_curl('search', $data);
        foreach ($out['data'] as &$value) {
            $value['shop_price'] = intval(trim($value['shop_price'], '￥'));
        }
        $data = $out['data'];
        $this->assertTrue($data[0]['shop_price'] >= $data[1]['shop_price']);
    }
    
    /**
     * @depends testOne
     */
    function testThree($out2)
    {
        $data = array(
            'pagination' => array('page' => 1, 'count' => 100),
            'sort_by' => 'is_hot'
        );
        $out1 = request_by_curl('search', $data);

        // $data = $out['data'];
        $this->assertTrue(count($out1['data']) < count($out2['data']));
    }
}