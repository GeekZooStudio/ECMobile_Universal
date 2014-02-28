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

class regionTest extends PHPUnit_Framework_TestCase
{
    function testOne()
    {
        $data = array(
            'parent_id' => 0
        );
        $out = request_by_curl('region', $data);
        $this->assertTrue(is_array($out['data']['regions']));
        return $out;
    }
    
    /**
     * @depends testOne
     */
    function testTwo($out)
    {
        $item = array_shift($out['data']['regions']);
        $data = array(
            'parent_id' => $item['id']
        );
        $out = request_by_curl('region', $data);
        $this->assertTrue(is_array($out['data']['regions']));
        
        return $out;
    }
    
    /**
     * @depends testTwo
     */
    function testThree($out)
    {
        shuffle($out['data']['regions']);
        $item = array_shift($out['data']['regions']);
        $data = array(
            'parent_id' => $item['id']
        );
        $out = request_by_curl('region', $data);
        $this->assertTrue(is_array($out['data']['regions']));
        
        return $out;
    }
    
    /**
     * @depends testThree
     */
    function testFour($out)
    {
        $item = array_shift($out['data']['regions']);
        $data = array(
            'type' => 3,
            'parent_id' => $item['id']
        );
        $out = request_by_curl('region', $data);
        $this->assertTrue(is_array($out['data']['regions']));
    }
}

