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

include EC_PATH.'/includes/cls_template.php';

class GZ_Smarty extends cls_template
{
	public $apiName;
	function __construct($apiName)
	{
		parent::__construct();
		$this->apiName = $apiName;

	}
	
	function display()
	{
		if (empty($this->vars) && empty($this->_var)) {
			return false;
		}
		if (isset($this->vars)) {
			$this->_var = $this->vars;
		}

		switch ($this->apiName) {
			// case 'search':
			// 	$data = array();
			// 	$data['goods'] = API_DATA("SIMPLEGOODS", $this->_var['goods_list']);
			// 	if (!empty($this->_var['pager'])) {
			// 		$pager = array(
			// 				"total"  => $this->_var['pager']['record_count'],	 
			// 				"count"  => count($this->_var['goods_list']),
			// 				"more"   => empty($this->_var['pager']['page_next']) ? 0 : 1
			// 		);
			// 	} else {
			// 		$pager = NULL;
			// 	}
			// 	GZ_Api::outPut($data, $pager);
			// 	break;
			case 'list':
				print_r($this->_var);exit;
				GZ_Api::outPut(API_DATA("SIMPLEORDER", $this->_var['orders']));
				break;
			case 'goods':
			print_r($this->_var['goods']);exit;
				break;
			default:
				print_r($this->_var);
				break;
		}
	}
	
	// function is_cached()
	// {
	// 	return false;
	// }
}

?>
