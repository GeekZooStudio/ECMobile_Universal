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

class user_signupTest extends PHPUnit_Framework_TestCase
{

    
    function testTwo()
    {
        $data = array();
        $out = request_by_curl('user/signupFields', $data);

        return $out;
    }

    /**
     * @depends testTwo
     */
    function testOne($out)
    {
        $data = array(
            'name' => 't'.time(),
            'password' => '123456789',
            'email' => 'test@'.time().'.com'
        );
        $fileld = array();
        foreach ($out['data'] as $val) {
            $fileld[] = array(
                'id' => $val['id'],
                'value' => rand(100, 900)
            );
        }
        $data['fileld'] = $fileld;

        $out = request_by_curl('user/signup', $data);
        $this->assertEquals(1, $out['status']['succeed']);
    }
}