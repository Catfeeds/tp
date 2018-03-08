<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');

function Login_test(){
	$url = "http://localhost/tp/index.php/user/Login";
	$test = array(
		'token'=>'8c4e323f771e226e1a89c72ee6efedaf',
		'username'=>'测试',
		'password'=>'d273890ae250557a0ba0ab479ef1cfd5',
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

function register(){
	$url = "http://localhost/tp/index.php/user/register";
	$test = array(
		'token'=>'8c4e323f771e226e1a89c72ee6efedaf',//apptoken
		'username'=>'ranlin',
		'password'=>'ranlin',
		'code'=>'222',
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
/*
 $test = array(
 'apptoken' =>'8c4e323f771e226e1a89c72ee6efedaf',
 'username' => '测试',
 'password' => 'd273890ae250557a0ba0ab479ef1cfd5',
 'code'=>'321',
 'usertoken'=>'6d9e06d7872ce516b1d79858f67bd3ae',
 'apptoken'=>'8c4e323f771e226e1a89c72ee6efedaf'
 );
 //************10.终止测试************
 $url = "http://localhost/tp/index.php/user/register";
 $params = array(
 'token'=>$test['apptoken'],
 'username'=>$test['username'],
 'password'=>$test['password'],
 'code'=>$test['code']
 );
 $paramstring = http_build_query($params);
 $content = juhecurl($url,$paramstring);
 $result = json_decode($content,true);
 test_result("register", $result);
 //**************************************************


 //************10.终止测试************
 $url = "http://localhost/tp/index.php/user/unregister";
 $params = array(
 'token'=>$test['usertoken'],
 );
 $paramstring = http_build_query($params);
 $content = juhecurl($url,$paramstring);
 $result = json_decode($content,true);
 test_result("unregister", $result);
 //**************************************************


 //************10.终止测试************
 $url = "http://localhost/tp/index.php/user/user_list";
 $params = array(
 'offset'=>'10',
 'limit'=>'10'
 );
 $paramstring = http_build_query($params);
 $content = juhecurl($url,$paramstring);
 test_result("user_list", $content);
 //**************************************************



 //************10.终止测试************
 $url = "http://localhost/tp/index.php/user/get_verify_code";
 $params = array(
 'phone'=>'13215227200',
 'token'=>$test["apptoken"]
 );
 $paramstring = http_build_query($params);
 $content = juhecurl($url,$paramstring);
 $result = json_decode($content,true);
 test_result("get_verify_code", $result);
 //**************************************************

 */