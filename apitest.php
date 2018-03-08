<style>
html {
	font-family: "Tahoma", "verdana", "arial", "sans-serif", "瀹嬩綋";
	font-size: 12px;
}

p {
	font-weight: bold;
	line-height: 1.6em;
	padding: 1px 0 8px 0;
	color: #DD0000;
}

pre {
	width: 87%;
	display: block;
	border: 1px #b7b8ba solid;
	padding: 10px;
	line-height: 1.2em;
	margin-bottom: 10px;
	background: #F6F6F6;
	font-size: 14px;
	word-wrap: break-wor;
	margin-bottom: 10px;
}
</style>
<?php
//----------------------------------
// APP兼容性测试调用
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
header('Content-type:text/html;charset=utf-8');

$test = array(
	'apptoken' =>'8c4e323f771e226e1a89c72ee6efedaf',
	'username' => '测试',
	'password' => 'd273890ae250557a0ba0ab479ef1cfd5',
	'imei' => '502151020042365',
	'usertoken' =>'2eec5c1e24dad385cf70edeacfdb3402',
	'bind' => 0,
	'interval' => 60,
	'name' => "ABC",
	'phone' => "12345678901",
	'appid' => "com.zlt.app.ione",
	'url' => "http://localhost/tp/index.php",
	'jpushid'=>'1',
	'user_id'=>'3621',
	'owner'=>'xiaoran',
	'device_id'=>'14529',
	'offset'=>'10',
	'lim'=>'10',
	'events'=>'1,2,3',
	'lng1'=>'116.478',
	'lat1'=>'39.7381',
	'lng2'=>'116.798',
	'lat2'=>'39.4913',
	'fence_id'=>'363'
);



























/**
 * 请求接口返回内容
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function juhecurl($url,$params=false,$ispost=0){
	$httpInfo = array();
	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
	curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if( $ispost )
	{
		curl_setopt( $ch , CURLOPT_POST , true );
		curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
		curl_setopt( $ch , CURLOPT_URL , $url );
	}
	else
	{
		if($params){
			curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
		}else{
			curl_setopt( $ch , CURLOPT_URL , $url);
		}
	}
	$response = curl_exec( $ch );
	if ($response === FALSE) {
		//echo "cURL Error: " . curl_error($ch);
		return false;
	}
	$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	curl_close( $ch );
	return $response;
}
/**
 * 测试结果
 * @param  string $result [测试结果]
 * @param  string $fun_name [测试的函数名称]
 */
function test_result($fun_name,$result){
	if($result){
		if($result['errcode']=='0'){
			echo "<p>".$fun_name."测试</p>";
			echo "<pre>错误编码：".$result['errcode']."<br/>内容:".json_encode($result)."</pre>";
		}else{
			echo "<p>".$fun_name."测试</p>";
			echo "<pre>错误编码：".$result['errcode']."<br/>内容:".$result['errmsg']."</pre>";
		}
	}else{
		echo "请求失败";
	}
}
?>