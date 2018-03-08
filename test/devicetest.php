
<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
header('Content-type:text/html;charset=utf-8');
include_once 'public.php';
$test = array(
	'app' =>'1',
	'type' => '0',
	'imei' => '502151020009767',
	'name' => "ABC",
	'owner'=>'xiaoran',
	'device_id'=>'14529',
	'card'=>'10000',
	'offset'=>'10',
	'limit'=>'10',
	'usertoken'=>'c99c530476af25bf28bcd2d5d819f873',
	'phone'=>'13215224322',
	'interval'=>'60'
);


function setdevicend_test(){
	$nd = array(
		'token'=>'65adfd1ab840dcc2e0131174ac1d60a5',
		'imei' => '502151020009767',
		'indexes'=>'20,21,22',
		'begins'=>'8:20,9:20,10:20',
		'ends'=>'8:30,9:30,10:30',
		'repeats'=>'0111015,0110106,0110101'
		);

		$url = "http://localhost/tp/index.php/device/setdevicend";
		if($params=$nd){
			$paramstring = http_build_query($params);
			$content = juhecurl($url,$paramstring);
			$obj = json_decode($content,true);
			if($obj['errcode']!="0"){
				file_put_contents("./error", "get_token:".$content."--".date('y-m-d h:i:s',time())."\r\n",FILE_APPEND);
			}
		}



}

setdevicend_test();
exit();
//************10.添加设备************
$url = "http://localhost/tp/index.php/device/device_add";
$params = array(
      "name" => $test['name'],
      "imei" => $test['imei'],
      "card" => $test['card'],
	  "type"=>'0',
	"app"=>'1',
	"owner"=>$test['owner'],
);

$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
test_result("device_add", $content);
//**************************************************



//************10.更改设备************
$url = "http://localhost/tp/index.php/device/device_update";
$params = array(
      "id" =>$test['device_id'],
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
echo $content;
$result = json_decode($content,true);
test_result("device_update", $result);
//**************************************************



//************10.终止测试************
$url = "http://localhost/tp/index.php/device/device_get_list";
$params = array(
      "offset" => $test['offset'],//登录时获取的sid
      "lim" => $test['limit'],//测试查询编号
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("device_get_list", $result);
//**************************************************



//************10.终止测试************
$url = "http://localhost/tp/index.php/device/device_set_info";
$params = array(
"token" => $test["usertoken"],//登录时获取的sid
"imei" => $test["imei"],//测试查询编号
"name"=>$test["name"],
"phone"=>$test["phone"],
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
test_result("device_set_info", $content);
//**************************************************



//************10.终止测试************
$url = "http://localhost/tp/index.php/device/device_get_info";
$params = array(
"usertoken" => $test["usertoken"],//登录时获取的sid
"imei" => $test["imei"],//测试查询编号
);
$paramstring = http_build_query($params);
echo $paramstring;
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("devcie_get_info", $result);
//**************************************************


//************10.终止测试************
$url = "http://localhost/tp/index.php/device/device_config";
$params = array(
"token" => $test['usertoken'],
"imei" => $test['imei'],
'interval'=>$test['interval']
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("device_config", $result);
//**************************************************


//************10.终止测试************
$url = "http://localhost/tp/index.php/device/getconf";
$params = array(
"token" => $test['usertoken'],
"imei" => $test['imei'],
'interval'=>$test['interval']
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("getconf", $result);
//**************************************************

//************10.删除设备************
$url = "http://localhost/tp/index.php/device/device_del";
$params = array(
      "id" =>$test['device_id'],
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
test_result("device_del", $result);
//**************************************************
