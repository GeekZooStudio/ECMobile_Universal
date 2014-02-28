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

error_reporting(E_ALL);

define('GZ_PATH', dirname(__FILE__));
define('EC_PATH', dirname(GZ_PATH));

// define('INIT_NO_SMARTY', true);

require GZ_PATH.'/Library/function.php';

spl_autoload_register('gz_autoload');

GZ_Api::init();

$url = _GET('url');

$controller = 'index';

$tmp = $url ? array_filter(explode('/', $url)) : array();

$path = GZ_PATH . '/controller';

$tmp = array_values($tmp);

//reset($tmp);
    
$count = count($tmp);
for ($i = 0; $i < $count; $i++) {
    if (!is_dir($path.'/'.$tmp[$i])) {
        break;
    }
    $path .= '/'.$tmp[$i];
}

if (isset($tmp[$i])) {
    $controller = $tmp[$i];
    $i++;
}

$file = $path.'/'.$controller.'.php';

$i && $tmp = array_slice($tmp, $i);

if (file_exists($file)) {
    define('IN_ECS', true);
    require $file;
} else {
	echo <<< ___END___

    <!DOCTYPE html>
<html class="no-js">
    <head>
        <title>ECMobile</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <p style="font-size: 14px;">请确认客户端配置填写正确，如需要技术支持：</p>
        <br/>
        <p style="font-size: 14px;">官方论坛：<a href="http://bbs.ecmobile.cn/">http://bbs.ecmobile.cn/</a></p>
        <p style="font-size: 14px;">QQ群1：329673575</p>
        <p style="font-size: 14px;">QQ群2：239571314</p>
        <p style="font-size: 14px;">QQ群3：347624547</p>
        <br/>
        <pre>                                                                           
     _/_/_/                      _/        _/_/_/_/_/                     
  _/          _/_/      _/_/    _/  _/          _/      _/_/      _/_/    
 _/  _/_/  _/_/_/_/  _/_/_/_/  _/_/          _/      _/    _/  _/    _/   
_/    _/  _/        _/        _/  _/      _/        _/    _/  _/    _/    
 _/_/_/    _/_/_/    _/_/_/  _/    _/  _/_/_/_/_/    _/_/      _/_/       
                                                                        

Copyright 2013-2014, Geek Zoo Studio
        </pre>

    </body>
</html>

___END___;

}