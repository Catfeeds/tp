<?php

//错误代码定义
define("ERRNO_SUCCESS", 0);
define("ERRNO_ARGUMENTS_ERROR", -1);
define("ERRNO_USER_ERROR", -2);
define("ERRNO_PRIVILEGE_ERROR", -3);
define("ERRNO_DEVICE_PRIVILEGE_ERROR", -4);
define("ERRNO_APP_ERROR", -5);
define("ERRNO_DEVICE_ERROR", -6);
define("ERRNO_LOCATION_ERROR", -7);
define("ERRNO_REGISTER_ERROR", -8);
define("ERRNO_AUTHORIZATION_REQUIRE", -9);
define("ERRNO_AUTHORIZATION_MASTER", -10);
define("ERRNO_OFFLINE", -11);
define("ERRNO_NOT_IMPLEMENTED", -12);
define("ERRNO_WAITING_REVIEW", -14);
define("ERRNO_VERIFY_CODE_ERROR", -15);
define("ERRNO_FAIL", -99);
define("DEVICE_ALREADY_BOND ", 1);
define("EXCEED_MAX_SIZE", -16);

//接口返回信息
function response($code, $msg, $data = null) {
    if (is_array($data)) {
        echo "{\"errcode\":$code,\"errmsg\":\"$msg\",\"data\":[" . join(',', $data) . "]}";
    } else {
        if ($data) {
            echo "{\"errcode\":$code,\"errmsg\":\"$msg\",\"data\":$data}";
        } else {
            echo "{\"errcode\":$code,\"errmsg\":\"$msg\"}";
        }
    }
    exit();
}

//传参数
function get_param($name, $default) {
    if ($default === null) {
        if (isset($_POST[$name]))
            return $_POST[$name];
        if (isset($_GET[$name]))
            return $_GET[$name];
        response(ERRNO_ARGUMENTS_ERROR, "not found $name");
    }
    if (isset($_POST[$name]) && $_POST[$name] != '')
        return $_POST[$name];
    if (isset($_GET[$name]) && $_GET[$name] != '')
        return $_GET[$name];
    return $default;
}

/*
 * 全表信息验证
 * @param char $table  需要验证的表名
 * @param char $filed    表字段名称
 * @param char $value  验证的字段值
 * @return json
 */

function get_table_message($table, $filed, $value) {
    if (!$table) {
        response(ERRNO_ARGUMENTS_ERROR, "not found" . $table);
    }
    if (!$filed) {
        response(ERRNO_ARGUMENTS_ERROR, "not found" . $filed);
    }
    $table_model = M($table);
    return $user_model->where($filed = $value)->find();
}

//验证用户
function check_user() {
    $zlt_user = M("zlt_user");
    $user = $zlt_user->where("user_id='$usertoken[usertoken_user]'")->find();
    if (!$user) {
        response(ERRNO_USER_ERROR, "user error!");
    } else {
        return $user;
    }
}

//验证用户token
function check_user_token($token) {
    $zlt_usertoken = M("zlt_usertoken");
    $usertoken = $zlt_usertoken->where("usertoken_token='$token'")->find();
    if (!$usertoken) {
        response(ERRNO_USER_ERROR, "usertoken error!");
    } else {
        return $usertoken;
    }
}

//验证设备
function check_device($imei) {
    $zlt_device = M("zlt_device");
    $where['device_imei'] = $imei;
    $device = $zlt_device->where($where)->find();
    if (!$device) {
        response(ERRNO_DEVICE_ERROR, "device error!");
    } else {
        return $device;
    }
}

//通过user_token 检查用户
function check_user_by_usertoken($token) {
    $zlt_usertoken = M("zlt_usertoken");
    $zlt_user = M("zlt_user");
    $usertoken = $zlt_usertoken->where("usertoken_token='$token'")->find();
    if (!$usertoken) {
        response(ERRNO_USER_ERROR, "usertoken error!");
    } else {
        $user = $zlt_user->where("user_id='$usertoken[usertoken_user]'")->find();
        if (!$user) {
            response(ERRNO_USER_ERROR, "user error!");
        } else {
        	$user['token'] = $usertoken;
            return $user;
        }
    }
}

//检查绑定情况
function check_bind($user_id, $device_id) {
    $zlt_bind = M("zlt_bind");
    $bind = $zlt_bind->where("bind_user='$user_id' and bind_device='$device_id' and bind_valid=1")->find();

    if (!$bind) {
        response(ERRNO_DEVICE_PRIVILEGE_ERROR, "device privilege error");
    } else {
        return $bind;
    }
}

//验证app_token
function check_apptoken($apptoken) {
    $zlt_apptoken = M("zlt_apptoken");
    $appid = $zlt_apptoken->where("apptoken_token='$apptoken'")->find();
    if (!$appid) {
        response(ERRNO_APP_ERROR, "apptoken error!");
    }
    return $appid;
}

//验证app
function check_app_by_apptoken($apptoken) {
    $appid = check_apptoken($apptoken);
    $zlt_app = M("zlt_app");
    $app = $zlt_app->where("app_id='$appid[apptoken_app]'")->find();
    if (!$app) {
        response(ERRNO_APP_ERROR, "app error!");
    }
    return $app;
}

//重写SAM_MQTT
function SAM_MQTT($User_id, $Content, $Mes) {
    $conn = new SAMConnection();
    $conn->connect(SAM_MQTT, array(SAM_HOST => C('MQTT_HOST'),
        SAM_PORT => C('MQTT_PORT'),
        SAM_MQTT_USER => C('MQTT_USER'),
        SAM_MQTT_PASS => C('MQTT_PASS'),
        SAM_MQTT_ID => 'u' . str_pad($User_id, 14, '0', STR_PAD_LEFT),
    ));
    $msgCpu = new SAMMessage($Mes);
    $conn->send($Content, $msgCpu);
    $conn->disconnect();
}

//数据传输
function build_http($url, $data = '', $method = 'POST', $wait=0) {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    $data = array();
    if ($method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        if ($data != '') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        }
    }
    if( $wait == 0 ){
    	$wait = 5;
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, $wait); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

function doGet($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

//获取设备状态
function get_device_status($imei) {
    $url = C('MQTT_HOST') . ":" . C('DEVICE_HOST_PORT') . "/device/" . $imei . "/host";
    $rel = json_decode(doGet($url), TRUE);
    if( $rel['errcode'] != 0 ){
    	return json_encode(['errcode'=>0, 'data'=>['active'=>0]]);
    }
    $post_url = $rel['data']['mqtt']['host'] . ":" . $rel['data']['web']['port'] . "/device/" . $imei;
    return build_http($post_url, "", "post");
}

//gps转高德
function convert_coords($locs) {
    $url = 'http://restapi.amap.com/v3/assistant/coordinate/convert?key=' . C('AMAP_KEY') . '&output=json&coordsys=gps&locations=' . join(";", $locs);
    try {
        $ch = curl_init();
        //参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $str = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($str);
        if ($obj->status != 1) {
            response(ERRNO_LOCATION_ERROR, 'convert failed');
        }
        $locstrs = split(';', $obj->locations);
        foreach ($locstrs as $loc) {
            $coord = split(',', $loc);
            $result[] = array('lng' => $coord[0], 'lat' => $coord[1]);
        }
    } catch (\Exception $e) {
        //echo $e;
        response(ERRNO_LOCATION_ERROR, 'convert failed(' . $e->message . ')');
    }
    return $result;
}
//远程拍照
function photo($imei, $fromuser,$sid){
    $get_url = C('MQTT_HOST') . ":" . "4001" . "/device/" . $imei . "/host";
    $rel = json_decode(doGet($get_url), TRUE);
    if( $rel['errcode'] != 0 ){
    	response(ERRNO_OFFLINE, 'device offline');
    }
    $post_url = $rel['data']['web']['host'] . ":" . $rel['data']['web']['port'] . "/device/".$imei."/takephoto/".$fromuser."/".$sid;
//    $post_url = "119.23.18.15" . ":" . "4001" . "/device/".$imei."/takephoto/".$fromuser."/".$sid;
    return build_http($post_url, '', 'POST');
}

//通知服务器
function tail_device($imei, $opt, $require_online=1, $wait=0) {
    $get_url = C('MQTT_HOST') . ":" . C('DEVICE_HOST_PORT') . "/device/" . $imei . "/host";
    $rel = json_decode(doGet($get_url), TRUE);
    
    if( $require_online && (!$rel || $rel['errcode'] != 0) ){
    	response(ERRNO_OFFLINE, 'device offline');
    }
    $post_url = $rel['data']['web']['host'] . ":" . $rel['data']['web']['port'] . "/device/" . $imei . "/" . $opt;
    return build_http($post_url, '', 'POST', $wait);
}

//数组转json
function arrayToObject($arr) {
    if (is_array($arr)) {
        return (object) array_map(__FUNCTION__, $arr);
    } else {
        return $arr;
    }
}
?>
