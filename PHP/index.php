<?php
error_reporting(E_ALL);

define('GZ_PATH', dirname(__FILE__));
define('EC_PATH', dirname(GZ_PATH));

// define('INIT_NO_SMARTY', true);

require GZ_PATH.'/library/function.php';

spl_autoload_register('gz_autoload');

GZ_Api::init();

$url = _GET('url');

$controller = 'index';

$tmp = $url ? array_filter(explode('/', $url)) : array();

$path = GZ_PATH . '/controller';

$payment = _GET('payment');
if ($payment) 
{
    $path = GZ_PATH . '/payment';
    $tmp = $payment ? array_filter(explode('/', $payment)) : array();
}

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
    <body style="padding: 10px;">

        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->

        <p style="font-size: 14px;">感谢使用ECMobile产品</p>
        <p style="font-size: 14px;">官方论坛：<a href="http://bbs.ecmobile.cn/">http://bbs.ecmobile.cn/</a></p>
        <p style="font-size: 14px;">QQ群1：329673575</p>
        <p style="font-size: 14px;">QQ群2：239571314</p>
        <p style="font-size: 14px;">QQ群3：347624547</p>
        <br/>
        <p style="font-size: 14px;">接下来，您可以：</p>
        <p style="font-size: 14px;">API测试：<a target="_blank" href="./test">点击这里</a></p>
        <p style="font-size: 14px;">API文档：<a target="_blank" href="./test/ecmobile.json">点击这里</a></p>

    </body>
</html>

___END___;

}