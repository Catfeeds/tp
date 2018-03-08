<?php

namespace Home\Controller;

use Think\Controller;

class DebugController extends Controller {
	public function index($imei='', $token='') {
		$this->assign("imei", $imei);
		$this->assign("token", $token);
		$this->display();
	}
	
	private function push($token, $imei, $title, $extras){
		require_once (PUB_PATH . 'vendor/jpush/autoload.php');
		require_once ('jpush.php');
		
		$user = check_user_by_usertoken($token);
		
		$device = check_device($imei);
		
		
		$extras['imei'] = $imei;
		$extras['device_name'] = $device['device_name'];
		
		$userreq = $user['user_id'];
				
		$payload = $jpushClient->push()
			->setPlatform('all')
			->addAlias('u' . str_pad($userreq, 14, '0', STR_PAD_LEFT))
			->androidNotification($title, array(
					'title' => $title,
					'extras' => $extras,
			))
			->iosNotification($title, array(
					'sound' => 'sound.caf',
					'badge' => '+1',
					'extras' => $extras,
			))
			->options(array(
					'apns_production' => false,
			))
			->send();
		
		$zlt_event = M("zlt_event");
		$data1[event_type] = $extras[type];
		$data1[event_recipient] = $userreq;
		$data1[event_extra] = json_encode($extras);
		$data1[event_jpushid] = $payload['body']['msg_id'];
		$zlt_event->data($data1)->add();
	}
	
	public function login($username, $password){
		$userrepo = M('zlt_user');
		$user = $userrepo->where(['user_name'=>$username,'user_pswd'=>md5($password)])->find();
		if( !$user ){
			response(ERRNO_FAIL, 'fail');
		}
		$usertokenrepo = M('zlt_usertoken');
		$data = [
				'usertoken_user'=>$user['user_id'],
				'usertoken_token'=>md5($user['user_name'].$user['user_pswd'].time())
		];
		$usertokenrepo->data($data)->add();
		response(ERRNO_SUCCESS, 'success', json_encode(['token'=>$data['usertoken_token']]));
	}
	
	public function fence($token, $imei, $fine, $status){
		$title = '围栏提醒';
		$extras = array('type' => 1,  'fine'=>$fine, 'status'=>$status, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function low_power($token, $imei, $power){
		$title = '低电提醒';
		$extras = array('type' => 2, 'power'=>$power, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function bind_req($token, $imei, $user, $msg){
		$title = '绑定请求';
		$extras = array('type' => 3, 'user'=>$user, 'msg'=>$msg, 'time' => time());
		
		$user_name = "请求用户";
		$userrepo = M("zlt_user");
		$user = $userrepo->where(['user_id'=>$user])->select();
		if( $user ){
			$user_name = $user['user_name'];
		}
		$extras['user_name'] = $user_name;
		
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function bind_rsp($token, $imei, $result){
		$title = '绑定审核结果';
		$extras = array('type' => 4, 'result' => $result, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function voice($token, $imei, $voice, $duration, $url){
		$title = '新语音';
		$extras = array('type' => 5, 'voice'=>$voice, 'duration'=>$duration, 'url'=>$url, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function sos($token, $imei, $lat, $lng, $address){
		$title = 'SOS报警';
		$extras = array('type' => 6, 'lat'=>$lat, 'lng'=>$lng, 'address'=>$address, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function photo($token, $imei, $photo, $sid, $url){
		$title = '新照片';
		$extras = array('type' => 7, 'photo' => $photo, 'sid'=>$sid, 'url'=>$url, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		
		response(ERRNO_SUCCESS, "success");
	}
	
	public function sms($token, $imei, $text){
		$title = '短信';
		$extras = array('type' => 8, 'text' =>$text, 'time' => time());
		$this->push($token, $imei, $title, $extras);
		response(ERRNO_SUCCESS, "success");
	}
}

?>