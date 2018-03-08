<?php

namespace Home\Controller;

use Think\Controller;

class SleepController extends Controller {
	function index($token, $imei){
		
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		
		$bind = check_bind($user['user_id'], $device['device_id']);
		$zlt_devicesleep = M("zlt_devicesleep");
		$field = "devicesleep_id as `index`,devicesleep_begin,devicesleep_end,devicesleep_repeat as `repeat`";
		$rs = $zlt_devicesleep->field($field)->where("devicesleep_device='$device[device_id]'")->select();
		$list = array();
		foreach ($rs as $r) {
			$data['id'] = $r['id'];
			$data['begin'] = '' . str_pad((int) ($r['devicesleep_begin'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($r['devicesleep_begin'] % 60, 2, '0', STR_PAD_LEFT);
			$data['end'] = '' . str_pad((int) ($r['devicesleep_end'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($r['devicesleep_end'] % 60, 2, '0', STR_PAD_LEFT);
			$data['repeat'] = $r['repeat'];
			$list[] = json_encode($data);
		}
		response(ERRNO_SUCCESS, "success", $list);
	}
	
	function view($token, $id){
		
		$user = check_user_by_usertoken($token);
		
		$zlt_devicesleep = M("zlt_devicesleep");
		$field = "devicesleep_id as `index`, devicesleep_device, devicesleep_begin, devicesleep_end,devicesleep_repeat as `repeat`";
		$r = $zlt_devicesleep->field($field)->where("devicesleep_id=$id")->find();
		$bind = check_bind($user['user_id'], $r['devicesleep_device']);

		$data['id'] = $r['id'];
		$data['begin'] = '' . str_pad((int) ($r['devicesleep_begin'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($r['devicesleep_begin'] % 60, 2, '0', STR_PAD_LEFT);
		$data['end'] = '' . str_pad((int) ($r['devicesleep_end'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($r['devicesleep_end'] % 60, 2, '0', STR_PAD_LEFT);
		$data['repeat'] = $r['repeat'];
	
		response(ERRNO_SUCCESS, "success", json_encode($data));
	}
	
	function create($token, $imei, $begin, $end, $repeat){
		
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		
		$begins = explode(":", $begin);
		$ends = explode(":", $end);
		$bind = check_bind($user['user_id'], $device['device_id']);
		$data['devicesleep_device'] = $device['device_id'];
		$data['devicesleep_begin'] = $begins[0] * 60 + $begins[1];
		$data['devicesleep_end'] = $ends[0] * 60 + $ends[1];
		$data['devicesleep_repeat'] = $repeat;
		$zlt_devicesleep = M("zlt_devicesleep");
		$ids = $zlt_devicesleep->add($data);
		tail_device($imei, "sleep");
		response(ERRNO_SUCCESS, "success", json_encode($ids));
	}
	
	function delete($token, $id){
		
		$zlt_devicesleep = M('zlt_devicesleep');
		$ids = $zlt_devicesleep->where("devicesleep_id=$id")->delete();
		
		tail_device($imei, "sleep");
		response(ERRNO_SUCCESS, "success", json_encode($ids));
	}
	
	function update($token, $id, $begin='', $end='', $repeat=''){
		
		$begins = explode(":", $begin);
		$ends = explode(":", $end);
		if( $begin ){
			$data['devicesleep_begin'] = $begins[0] * 60 + $begins[1];
		}
		if( $end ){
			$data['devicesleep_end'] = $ends[0] * 60 + $ends[1];
		}
		if( $repeat ){
			$data['devicesleep_repeat'] = $repeat;
		}
		$zlt_devicesleep = M('zlt_devicesleep');
		$ids = $zlt_devicesleep->where("devicesleep_id=$id")->save($data);
		
		tail_device($imei, "sleep");
		response(ERRNO_SUCCESS, "success", json_encode($ids));
	}
}

?>