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

class address_listTest extends PHPUnit_Framework_TestCase
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
    function testgetUserAddressList($session)
    {
        $data = array(
            'session' => $session
        );

        $out = request_by_curl('address/list', $data);
        $this->assertEquals(1, $out['status']['succeed']);

        return $session;
    }
}