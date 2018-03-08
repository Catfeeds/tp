<?php
// +----------------------------------------------------------------------
// | JuhePHP [ NO ZUO NO DIE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2015 http://juhe.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Juhedata <info@juhe.cn-->
// +----------------------------------------------------------------------
 
//----------------------------------
// APP兼容性测试调用示例代码 － 聚合数据
// 在线接口文档：http://www.juhe.cn/docs/113
//----------------------------------
 
header('Content-type:text/html;charset=utf-8');

 
 
 
 
//************1.用户登录************
$url = "http://api2.juheapi.com/testin/login";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************2.获取测试机型列表************
$url = "http://api2.juheapi.com/testin/specimens/get";
$params = array(
      "sysfname" => "",//设备系统名称，android/ios(目前ios未开放)
      "minosver" => "",//最小的系统版本信息
      "brands" => "",//品牌id [1,2,3]
      "models" => "",//机型id [1,2,3]
      "resolutions" => "",//机型分辨率 [240*320]
      "osvers" => "",//系统版本 [1.5,2.1]
      "screensizes" => "",//屏幕尺寸 [3.2,5.0]
      "sid" => "",//用户身份GUID
      "key" => $appkey,//应用APPKEY(应用详细页查询)
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************3.提交测试************
$url = "http://api2.juheapi.com/testin/app/add";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
      "packageurl" => "",//应用包下载地址
      "syspfid" => "",//系统id 1-android、2-ios（iOS暂未开放）
      "models" => "",//测试机型列表（jsonarrayStr,例:[{"modelId":10606,"releaseVer":"4.3"}]）
      "testtype" => "",//测试类型 0-兼容测试 1-功能测试
      "script" => "",//创建功能测试必选参数；兼容测试不选
      "appname" => "",//测试应用名称，可自动读取
      "appversion" => "",//测试应用版本，可自动读取
      "subsource" => "",//测试分类或批次，方便批量管理
      "sid" => "",//登录获取的sid
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************4.查询提交测试结果************
$url = "http://api2.juheapi.com/testin/app/add/result";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
      "adaptId" => "",//测试查询编号
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************5.查询测试报告概况************
$url = "http://api2.juheapi.com/testin/report/overview";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
      "sid" => "",//登录获取的sid
      "adaptid" => "",//测试查询编号
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************6.查询测试的报告详细数据************
$url = "http://api2.juheapi.com/testin/report/details";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
      "sid" => "",//登录获取的sid
      "reportid" => "",//报告编号
      "filter" => "",//0 不过滤重测的历史记录， 1 过滤重测的历史记录。 默认为 0
      "adaptid" => "",//测试查询编号
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************7.查询App的安全检测信息************
$url = "http://api2.juheapi.com/testin/report/details/get";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
      "sid" => "",//登录获取的sid
      "channel" => "",//数据渠道信息
      "adaptid" => "",//测试查询编号
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************8.Pdf报告生成************
$url = "http://api2.juheapi.com/testin/report/pdf/create";
$params = array(
      "adaptid" => "",//测试查询编号
      "sid" => "",//登录时获取的sid
      "key" => $appkey,//应用APPKEY(应用详细页查询)
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************9.测试列表查询************
$url = "http://api2.juheapi.com/testin/app/list";
$params = array(
      "key" => $appkey,//应用APPKEY(应用详细页查询)
      "sid" => "",//登录时获取的sid
      "offset" => "",//从第几条数据开始（默认为0）
      "max" => "",//最大查询条数（默认为15，不超过100）
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
//************10.终止测试************
$url = "http://api2.juheapi.com/testin/app/test/stop";
$params = array(
      "sid" => "",//登录时获取的sid
      "adaptid" => "",//测试查询编号
      "key" => $appkey,//应用APPKEY(应用详细页查询)
);
$paramstring = http_build_query($params);
$content = juhecurl($url,$paramstring);
$result = json_decode($content,true);
if($result){
    if($result['error_code']=='0'){
        print_r($result);
    }else{
        echo $result['error_code'].":".$result['reason'];
    }
}else{
    echo "请求失败";
}
//**************************************************
 
 
 
 
 
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
?>