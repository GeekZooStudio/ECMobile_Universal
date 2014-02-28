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

class address_updateTest extends PHPUnit_Framework_TestCase
{
	function testuserLogin()
    {
        $data = array(
            'name' => 'aaa',
            'password' => '123123123'
        );

        $out = request_by_curl('user/signin', $data);
		//print_r($out);
        $this->assertEquals(1, $out['status']['succeed']);
        return $out['data']['session'];
    }

    /**
     * @depends testuserLogin
     */
	 
	function testReadAddress($session) {
		$data = array(
            'session' => $session
        );
		
		$out = request_by_curl('address/list', $data);
		$this->assertEquals(1, $out['status']['succeed']);
		$out['data']['session'] = $session;
		return $out['data'];
	}
	
	/**
     * @depends testReadAddress
     */
   function testupdateAddress($out)
    {
		$data = array(

            'session' => $out['session'],
            'address_id' => $out[0]['id'],
			'address' => array(
				'country'=>1,
				'province'=>3,
				'city'=>36,
				'district'=>400,
				'consignee'=>'小王',
				'email'=>'471010089@qq.com',
				'address'=>'旺卒中心',
				'zipcode'=>'050000',
				'mobile'=>'13930111111',
				'tel'=>'88009933',
				'sign_building'=>'中央裤衩',
				'best_time'=>'23123123123123',
			)
        );
		$out = request_by_curl('address/update', $data);

        $this->assertEquals(1, $out['status']['succeed']);
		 //return $session;
    }
}