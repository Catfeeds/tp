<?php

namespace Home\Controller;

use Think\Controller;

class PhoneLogController extends Controller {
	function index($token, $imei, $begin='', $end=''){
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		
		$zlt_devicephonelog = M('zlt_devicephonelog');
		
		$query['phonelog_device'] = $device['device_id'];
		if( $begin && $end ){
			$query['phonelog_time'] = array('between', $begin, $end);
		}
		
		$rs = $zlt_devicephonelog->where($query)->select();
		
		$result = [];
		foreach($rs as $r){
			$data['id'] = $r['phonelog_id'];
			$data['time'] = $r['phonelog_time'];
			$data['peer'] = $r['phonelog_peer'];
			$data['dir'] = $r['phonelog_dir'];
			$data['duration'] = $r['phonelog_dur'];
			
			$result[] = json_encode($data);
		}
		response(ERRNO_SUCCESS, 'success', $result);
	}
}

?>