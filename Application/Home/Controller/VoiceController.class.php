<?php
namespace Home\Controller;
use Think\Controller;
class VoiceController extends Controller{
	//监听ok
	function passivecall(){
		$token = get_param('token',null);
		$imei = get_param('imei',null);
		$number = get_param('number',null);
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$zlt_bind = M("zlt_bind");
		$bind = $zlt_bind->where("bind_user=$user[user_id] and bind_device=$device[device_id] and bind_valid=1")->find();
		if ( !$bind ) {
			response(ERRNO_DEVICE_PRIVILEGE_ERROR, "device privilege error!");
		}
//		require_once(PUB_PATH.'device_status_cache/status.php');
//		if ( !device_status($imei) ) {
//			response(ERRNO_OFFLINE, "device offline");
//		}

		$status = json_decode(get_device_status($imei), true);
		if (!$status['data']['active']) {
			response(ERRNO_OFFLINE, "device offline");
		}
		
//		require(PUB_PATH.'/SAM/php_sam.php');
//		$conn = new SAMConnection();
//		$conn->connect(SAM_MQTT, array(SAM_HOST => '127.0.0.1',
//		SAM_PORT => 1883,
//		SAM_MQTT_ID => 'u'.str_pad($use[user_id], 14, '0', STR_PAD_LEFT),
//		SAM_MQTT_USER => 'dog-devsrv',
//		SAM_MQTT_PASS => '484848'));
//		$msgCpu = new SAMMessage($number."\0");
//		$conn->send('topic://'.$imei.'/'.$imei.'/call', $msgCpu);
//		$conn->disconnect();
		$url = C('DEVICE_HOST').":".C('DEVICE_HOST_PORT')."/device/".$imei."/monitor/".$user['user_name'];
		build_http($url);
		response(ERRNO_SUCCESS, "success");
	}
	//发送声音 Ok
	function postvoice(){
		$token = get_param('token', null);
		$imei = get_param('imei', null);
		$url = get_param('url', null);
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$zlt_bind = M("zlt_bind");
		$bind =$zlt_bind -> where("bind_user='$user[user_id]' and bind_device='$device[device_id]' and bind_valid=1")->find();
		if ( !$bind ) {
			response(ERRNO_DEVICE_PRIVILEGE_ERROR, "device privilege error!");
		}

		$zlt_voice = M("zlt_voice");
		$voice[voice_from] = 'u'.$user[user_name];
		$voice[voice_to] = 'd'.$imei;
		$voice[voice_url] = $url;
		$voice[voice_post] = 1;
		$voice_id=$zlt_voice->add($voice);		
        tail_device($imei, "voice/$user[user_name]/$voice_id");
		response(ERRNO_SUCCESS, "success", array("id"=>$voice_id));
	}
	//得到语音列表 
	function getvoicelist(){
		$token = get_param('token',null);
		$imei = get_param('imei',null);
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user["user_id"],$device["device_id"]);
		$data = array();
		$zlt_voice = M("zlt_voice");
		$map["voice_from"] =  "d".$imei;
		$map["voice_read"] = "1";
		$data = $zlt_voice->where($map)->select();
		response(ERRNO_SUCCESS, "success", json_encode($data));
	}
	//得到报警列表(不做)
	function GetAlarmList(){
	}
	//根据声音的id查找语音 ，且语音状态标识为1
	function getvoice(){
		$token = get_param('token',null);
		$imei = get_param('imei',null);
		$id = get_param('id',null);//声音的id
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user["user_id"],$device["device_id"]);
		$zlt_voice = M("zlt_voice");

		$map["voice_from"] =  "d".$imei;
		$map["voice_read"] = "1";
		$map["voice_id"] = $id;
		$data = $zlt_voice->where($map)->select();
		response(ERRNO_SUCCESS, "success", json_encode($data));
	}
}
