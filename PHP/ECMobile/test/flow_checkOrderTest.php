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

class flow_checkOrderTest extends PHPUnit_Framework_TestCase
{
    function testuserLogin()
    {
        $data = array(
            'name' => 'xuanyan',
            'password' => '123456789'
        );

        $out = request_by_curl('user/signin', $data);
        $this->assertEquals(1, $out['status']['succeed']);
        return $out['data']['session'];
    }

    /**
     * @depends testuserLogin
     */
    function testsearchOneGoodsAndGetInfo($session)
    {
        $data = array(
            'session' => $session,
            'pagination' => array('page' => 1, 'count' => 1)
        );
        $out = request_by_curl('search', $data);
        $this->assertEquals(1, $out['status']['succeed']);
        $item = $out['data'][0];
        $data = array(
            'session' => $session,
            "goods_id" => $item['goods_id']
        );
        $out = request_by_curl('goods', $data);
        $this->assertEquals(1, $out['status']['succeed']);
        
        $out = array(
            'session' => $session,
            'goods' => $out['data']
        );
       // $this->assertEmpty($out['data']['goods_list']);

        return $out;
    }

    /**
     * @depends testsearchOneGoodsAndGetInfo
     */
    function testaddToCart($data)
    {
        $session = $data['session'];
        $goods = $data['goods'];



        $spec = array();

        if (!empty($goods['specification'])) {
            $spec[] = $goods['specification'][0]['value'][0]['id'];
        }
        $data = array(
            'goods_id' => $goods['id'],
            'session' => $session,
            'spec' => $spec,
            'number' => 2
        );
        $out = request_by_curl('cart/create', $data);
        //$this->assertEmpty($out['data']['goods_list']);

        return $session;
    }
    
    /**
     * @depends testaddToCart
     */
    function testflowCheckorder($session)
    {
        $data = array(
            'session' => $session
        );

        $out = request_by_curl('flow/checkOrder', $data);
        
        $this->assertCount(1, $out['data']['goods_list']);

        $this->assertArrayHasKey('payment_list', $out['data']);
        $this->assertArrayHasKey('shipping_list', $out['data']);
    }
}