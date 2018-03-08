<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');

function dealevents_test(){
	$url = "http://localhost/tp/index.php/event/dealevents";
	$test = array(
    	"token" =>'3daece313e28aa069a95bd0007d09e48',
		"events"=>'12,13,14',
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


function pullevents_test(){
	$url = "http://localhost/tp/index.php/event/pullevents";
	$test = array(
     "token" => '3daece313e28aa069a95bd0007d09e48',
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




