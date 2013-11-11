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

function request_by_curl($url,$data){
    // $dir = str_replace('/', '_', $url);
    // $dir = __DIR__."/log/$dir";
    // if (!file_exists($dir)) {
    //     mkdir($dir);
    // }
    //file_put_contents($dir.'/request.txt', var_export($data, true));
    $apiurl = $url;
    $url = sprintf('http://dev.ecmobile.me/ecmobile/?url=%s', $url);
    $data = array(
        'json' => json_encode($data)
    );
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    $data = @json_decode($output, true);
    if ($data) {
        //file_put_contents($dir.'/response.txt', var_export($data, true));
        return $data;
    }
    //file_put_contents($dir.'/response.txt', $output);
    
    throw new Exception($url."\n".$output, 1);
}