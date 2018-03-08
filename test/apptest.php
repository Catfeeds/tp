<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');
function get_token_test(){
	$get_token_test_bianjie=array(
		'appid' =>'-1355eda6',
		'password'=>'-1asda',
		'time'=>'2145355',
	);
	$get_token_true=array(
		'appid' => 'com.zlt.app.ione',
		'password'=>'d273890ae250557a0ba0ab479ef1cfd5',
		'time'=>'2016-08-27 05:49:35',
	);
	//************1.get_token************
	$url = "http://localhost/tp/index.php/app/get_token";
	//测试边界
	if($params=$get_token_test_bianjie){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="-5"){
			file_put_contents("./error", "get_token:".$content."--".time()."\r\n",FILE_APPEND);
		}
	}
	//测试正确
	if($params=$get_token_true){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".time()."\r\n",FILE_APPEND);
		}
	}

}
get_token_test();

?>