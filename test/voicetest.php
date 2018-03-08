<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');

$test = array(
	'phone' => "12345678901",
	'voice_id'=>'20',
	'imei' => '502151020009767',
	'usertoken' =>'a478f8a9b799c9098c0eed20b5ca4f34',
	'url'=>'192.168.1.233',
);

function passivecall_test(){
	$url = "http://localhost/tp/index.php/voice/passivecall";
	$test = array(
		'token'=>'',
		'imei'=>'',
		'number'=>'',
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



function postvoice_test(){
	$url = "http://localhost/tp/index.php/voice/postvoice";
	$params = array(
		'url'=>'',
		'imei'=>'',
		'token'=>'',
		'number'=>'',
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


function getvoicelist_test(){
	$url = "http://localhost/tp/index.php/voice/getvoicelist";
	$test = array(
		'imei'=>'',
		'token'=>'',
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

function getvoice_test(){
	$url = "http://localhost/tp/index.php/voice/getvoice";
	$test = array(
		'token'=>'',
		'imei'=>'',
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



