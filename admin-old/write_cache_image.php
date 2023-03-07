<?php

require dirname(dirname(__FILE__)) . '/config.php';

function downloadImage($img_local){
    $pathInfo = explode('.',$img_local);
    if (end($pathInfo) !== 'jpg' && end($pathInfo) !== 'png') die;

    $size = defined('CFG_CACHE_ADMIN_IMAGES_SIZE') ? CFG_CACHE_ADMIN_IMAGES_SIZE : 160;
    $ch = curl_init($_REQUEST['src'].'_'.$size.'x'.$size.'.jpg');

    $fp = fopen($img_local, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.112 Safari/534.30");
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    return true;
}

$urlInfo = explode('/',$_REQUEST['src']);
$img_local = dirname(__FILE__).'/cache/'.end($urlInfo);
if(!file_exists(dirname(__FILE__).'/cache/')){
    mkdir(dirname(__FILE__).'/cache/', 0777);
}
if(!file_exists($img_local) || !@getimagesize($img_local)){
    @unlink($img_local);
    downloadImage($img_local);
}
print $img_local;