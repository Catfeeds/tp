
<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
require_once 'public.php';
header('Content-type:text/html;charset=utf-8');
function bindtest(){
	//************2.绑定用户与设备关系************
	//分支测试
	/*没绑定*/
	$nobind = array(
		'token1'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'token2'=>'8c4e323f771e226e1a89c72ee6efedaf',
		'imei' => '502151020009767',
		'nick'=>'0012',
	);
	/*以绑定*/
	$isbind = array(
		'token1'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'token2'=>'8c4e323f771e226e1a89c72ee6efedaf',
		'imei' => '502151020009767',
		'nick'=>'0012',
	);
	/*边界测试*/
	$overbind = array(
		'token1'=>'65adfd1ab8》》》》。。。。。。。。',
		'token2'=>'8c4e323f771e226%%%%去去去e1a89c72ee6efedaf',
		'imei' => '502151020009767aaaa',
		'nick'=>'001好2',
	);


	$url = "http://localhost/tp/index.php/bind/bind";

	if($params=$nobind){
		$paramstring = http_build_query($params);
				echo $paramstring;
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
	//测试已经绑定
	if($params=$isbind ){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
	//绑定边界测试
	if($params=$overbind ){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}
bindtest();
//
function BindMastertest(){
	//************4.转移管理员给指定的用户************
	$url = "http://localhost/tp/index.php/Bind/BindMaster";
	//分支测试
	$fenzhi = array(
		'token1'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'imei' => '502151020009767',
		'user'=>"1453",
	);
	$over = array(
		'token1'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'imei' => '502151020009767',
		'user'=>"werqwerqwerqw",
	);

	//绑定边界测试
	if($params=$over ){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
	//绑定边界测试
	if($params=$fenzhi ){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}

function bindReqtest(){
	//************8.绑定请求************
	$url = "http://localhost/tp/index.php/Bind/bindReq";
	$over = array(
		'token1'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'token2'=>'8c4e323f771e226e1a89c72ee6efedaf',
		'imei' => '502151020009767',
		"msg"=>'sdfasdf'
		);
		if($params=$over ){
			$paramstring = http_build_query($params);
			$content = juhecurl($url,$paramstring);
			$obj = json_decode($content,true);
			if($obj['errcode']!="0"){
				file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
			}
		}
}

function bindrsp(){
	//************9.绑定回复************
	$url = "http://localhost/tp/index.php/Bind/bindrsp";
	$test=array(
		'token1'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'token2'=>'8c4e323f771e226e1a89c72ee6efedaf',
		'imei' => '502151020009767',
		'user'=>'1453',
		"result" => '0',
	);
	if($params=$test ){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		$obj = json_decode($content,true);
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--". date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}

function unbind(){
	//************3.解除绑定************
	$url = "http://localhost/tp/index.php/Bind/unbind";
	$test = array(
      'token'=>'65adfd1ab840dcc2e0131174ac1d60a5',
      'imei' => '502151020009767',
	);
	if($params=$test ){
		$paramstring = http_build_query($params);
		$content = juhecurl($url,$paramstring);
		echo $content;
		$obj = json_decode($content,true);
		
		if($obj['errcode']!="0"){
			file_put_contents("./error", "get_token:".$content."--". date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
		}
	}
}

