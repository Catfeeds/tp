<?php
header('Content-type:text/html;charset=utf-8');
require_once 'public.php';
$test = array(
	'usertoken'=>'c68ca527b082d363846c140169f82f36',
	'coordtype'=>'wgs84',
	'imei' => '502151020009767',
	'begin'=>'2015-10-15 13:14:05',
	'end'=>'2017-10-15 13:14:05'
);


//************10.************
$url = "http://localhost/tp/index.php/Location/Location";
$params = array(
	'token' => $test['usertoken'],//登录时获取的sid
	'coordtype'=>'wgs84',
	'imei'=>$test['imei'],
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("Location", $result);
//**************************************************

//************10.终止测试************
$url = "http://localhost/tp/index.php/Location/history_fast";
$params = array(
	"token" => $test["usertoken"],//登录时获取的sid
	'coordtype'=>'wgs84',
	'imei'=>$test['imei'],
	'begin'=>'2015-10-15 13:14:05',
	'end'=>'2017-10-15 13:14:05'
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("history_fast", $result);
//*************************************************
?>