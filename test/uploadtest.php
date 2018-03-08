<?php

//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');

$test = array(
	'apptoken' =>'8c4e323f771e226e1a89c72ee6efedaf',
	'username' => '测试',
	'password' => 'd273890ae250557a0ba0ab479ef1cfd5',
	'code'=>'321',
	'usertoken'=>'6d9e06d7872ce516b1d79858f67bd3ae',
	'apptoken'=>'8c4e323f771e226e1a89c72ee6efedaf'
);




//************10.终止测试************
$url = "http://localhost/tp/index.php/upload/upload_photo";
$params = array(
'imie'=>'',
'lng'=>'',
'lat'=>''
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("upload_photo", $result);


//************10.终止测试************
$url = "http://localhost/tp/index.php/upload/upload";
$params = array(
'imie'=>'',
'lng'=>'',
'lat'=>''
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("upload_photo", $result);
//*

