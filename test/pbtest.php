<?php
header('Content-type:text/html;charset=utf-8');
require_once 'public.php';
function GetDevicePB_test(){
	$url = "http://localhost/tp/index.php/pb/GetDevicePB";
	$test = array(
	"token" => '427ff1de1b0e3f090f17921311c31d90',
	'imei'=>'502151020009767',
	);
	if($params=$test){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		echo $content;
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}


function SetDevicePB_test(){
	$url = "http://localhost/tp/index.php/pb/SetDevicePB";
	$test = array(
	"token" => 'ab049a5b0bcabf23ac691d6599f644cb',
	'imei'=>'502151080048692',
	'indexes'=>'1,2,8',
	'numbers'=>'121,132,133',
	'names'=>'白,富,美'
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
SetDevicePB_test();


?>