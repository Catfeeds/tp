
<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
header('Content-type:text/html;charset=utf-8');
require_once 'public.php';
function takephoto_test(){
	$url = "http://localhost/tp/index.php/photo/takephoto";
	$test = array(
	'usertoken'=>'',
	'imei' => '',
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

function get_photo_list_test(){
	$url = "http://localhost/tp/index.php/photo/get_photo_list";
	$test = array(
		"token" =>'',
		'imei'=> '',
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

?>