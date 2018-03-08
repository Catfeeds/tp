<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');
function fence_add_test(){
	$url = "http://localhost/tp/index.php/fence/fence_add";
	$test = array(
		'token' => '',//登录时获取的sid
		'imei'=>'',
		'name'=>'',
		'type'=>'',
		'lng1'=>'',
		'lng2'=>'',
		'lat1'=>'',
		'lat2'=>'',
	);
	if($params=$test){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}

//************10.更改围栏************
function fence_update_test(){
	$url = "http://localhost/tp/index.php/fence/fence_update";
	$test = array(
		'token' => '',//登录时获取的sid
		'imei'=>'',
		'name'=>'',
		'type'=>'',
		'lng1'=>'',
		'lng2'=>'',
		'lat1'=>'',
		'lat2'=>'',
		'id'=>'',
	);
	if($params=$test){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}

function fence_del_test(){
	$url = "http://localhost/tp/index.php/fence/fence_del";
	$test = array(
		'token' => '',//登录时获取的sid
		'id'=>'',
	);
	if($params=$test){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}
/*
 //************10.得到围栏列表************
 $url = "http://localhost/tp/index.php/fence/fence_get_list";
 $params = array(
 "token" => $test['usertoken'],
 );
 $paramstring = http_build_query($params);
 $content = juhecurl($url,$paramstring);
 $result = json_decode($content,true);
 test_result("fence_get_list", $result);
 //**************************************************
 */

