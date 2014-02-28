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

class user_signinTest extends PHPUnit_Framework_TestCase
{
    function testOne()
    {
        //密码正确
        $data = array(
            'name' => 'xuanyan',
            'password' => '123456789'
        );

        $out = request_by_curl('user/signin', $data);
        $this->assertEquals(1, $out['status']['succeed']);
    }

    function testTwo()
    {
        //密码错误
        $data = array(
            'name' => 'xuanyan',
            'password' => '12345678'
        );

        $out = request_by_curl('user/signin', $data);
        //print_r($out);
        $this->assertEquals(0, $out['status']['succeed']);
    }
}
?>