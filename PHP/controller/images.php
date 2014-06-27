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

$url = implode('/', $tmp);

if (@$_SERVER['HTTP_IF_MODIFIED_SINCE'] && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) - time() < 60)) {
    header("HTTP/1.1 304 Not Modified", true);
    exit;
}
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

$path = ltrim($url, '/');
list($m, $file) = explode('/', $path, 2);

if (empty($file)) {
    die("empty \$_GET['file']");
}

$param = explode('_', $m);

$file = EC_PATH.'/'.$file;
//echo $file;exit;
if (!file_exists($file)) {
    die("file is not exists");
}

$ext = pathinfo($file, PATHINFO_EXTENSION);

if ($ext == 'gif') {
    header("Content-Type: image/gif");
    readfile($file);
    exit;
}

$im = new Imagick();

try {
    $im->readImage($file);
} catch (Exception $e) {
    print_r($e);
    exit;
}

if ($param[0] == 'cropped' || $param[0] == 'fixed') {
    $width = intval(@$param[1]);
    $height = empty($param[2]) ? $width : intval($param[2]);
} else {
    $width = empty($param[0]) ? null :intval(@$param[0]);
    $height = empty($param[1]) ? null : intval($param[1]);
}

$width > 1000 && $width = 1000;
$height > 1000 && $height = 1000;

// bug when /_200/2.png
// if (empty($width)) {
//     die("width == 0");
// }

if ($param[0] == 'cropped' || $param[0] == 'fixed') {
    $m = $param[0] == 'cropped' ? 'cropThumbnailImage' : 'thumbnailImage';
    if (empty($height)) {
        die("height == 0");
    }
    $im->$m($width, $height);
} elseif ($height === null) {
    $im->thumbnailImage($width, $height);
} else {
    if (empty($height)) {
        die("height == 0");
    }
    $im->thumbnailImage($width, $height, true);
}
header("Content-Type: image/{$im->getImageFormat()}");
#header("Expires: Fri, 12 Nov 2010 10:42:29 GMT");
echo $im;
exit;