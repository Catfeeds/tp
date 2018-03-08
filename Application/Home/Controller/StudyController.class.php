<?php

namespace Home\Controller;

use Think\Controller;

class StudyController extends Controller {
	function index($token, $imei, $offset=null, $limit=null, $begin=null, $end=null){
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		
		$zlt_devicestudy = M('zlt_devicestudy');
		
		$query['devicestudy_device'] = $device['device_id'];
		if( $begin && $end ){
			$query['devicestudy_time'] = array('between', date('Y-m-d H:i:S',$begin), date('Y-m-d H:i:S',$end));
		}
		$field = 'count(devicestudy_id) as total,devicestudy_id, max(devicestudy_time) as time, devicestudy_code,studycode_name';
		$join = 'left join zlt_studycode on studycode_code=devicestudy_code';
		$q = $zlt_devicestudy->field($field)->where($query)->join($join)->order('devicestudy_time desc')->group('devicestudy_code');
		
		$total = $q->count();
		
		$q = $zlt_devicestudy->field($field)->where($query)->join($join)->order('devicestudy_time desc')->group('devicestudy_code');
		if( $offset != null && $limit != null){
			$q = $q->limit($offset, $limit);
		}
		
		
		$rs = $q->select();
		
		$result = [];
		foreach($rs as $r){
			$data['id'] = $r['devicestudy_id'];
			$data['time'] = $r['time'];
			$data['code'] = $r['devicestudy_code'];
			$data['name'] = $r['studycode_name'];
			$data['total'] = $r['total'];
			$result[] = json_encode($data);
		}
		$extra = null;
		if( $offset != null && $limit != null){
			$extra = ['offset'=>$offset, 'total'=>$total];
		}
		
		response(ERRNO_SUCCESS, 'success', $result, $extra);
	}
	
	function top($token, $imei, $count, $begin=null, $end=null){
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		
		$zlt_devicestudy = M('zlt_devicestudy');
		
		$query['devicestudy_device'] = $device['device_id'];
		if( $begin && $end ){
			$query['devicestudy_time'] = array('between', date('Y-m-d H:i:S',$begin), date('Y-m-d H:i:S',$end));
		}
		$field = 'count(devicestudy_id) as total,devicestudy_code,studycode_name';
		$join = 'left join zlt_studycode on studycode_code=devicestudy_code';
		$rs = $zlt_devicestudy->field($field)->where($query)->join($join)->group('devicestudy_code')->order('total desc')->limit($count)->select();
		$result = [];
		foreach($rs as $r){
			$data['code'] = $r['devicestudy_code'];
			$data['total'] = $r['total'];
			$data['study'] = $r['studycode_name']?$r['studycode_name']:$r['devicestudy_code'];
			
			$result[] = json_encode($data);
		}
		
		response(ERRNO_SUCCESS, 'success', $result);
	}
	
	function total($token, $imei, $begin=null, $end=null){
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		
		$zlt_devicestudy = M('zlt_devicestudy');
		
		$query['devicestudy_device'] = $device['device_id'];
		if( $begin && $end ){
			$query['devicestudy_time'] = array('between', date('Y-m-d H:i:S',$begin), date('Y-m-d H:i:S',$end));
		}
		$field = 'count(devicestudy_id) as total';
		$r = $zlt_devicestudy->field($field)->where($query)->find();
		
		$result['total'] = $r['total'];
		
		response(ERRNO_SUCCESS, 'success', json_encode($result));
	}
	
	function days($token, $imei, $count){
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		
		$zlt_devicestudy = M('zlt_devicestudy');
		
		$query['devicestudy_device'] = $device['device_id'];
		if( $begin && $end ){
			$query['devicestudy_time'] = array('between', date('Y-m-d H:i:S',$begin), date('Y-m-d H:i:S',$end));
		}
		$field = 'DATE_FORMAT(devicestudy_time,"%Y%m%d") as days, count(devicestudy_id) as total';
		$rs = $zlt_devicestudy->field($field)->where($query)->group('days')->limit($count)->select();
		$result = [];
		foreach($rs as $r){
			$data['day'] = $r['days'];
			$data['total'] = $r['total'];
			$result[] = json_encode($data);
		}
		
		response(ERRNO_SUCCESS, 'success', $result);
	}
	
	function totalBook($token, $imei, $begin=null, $end=null){
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		
		$zlt_devicestudy = M('zlt_devicestudy');
		
		$query['devicestudy_device'] = $device['device_id'];
		if( $begin && $end ){
			$query['devicestudy_time'] = array('between', date('Y-m-d H:i:S',$begin), date('Y-m-d H:i:S',$end));
		}
		$field = 'count(*) as total';
		$rs = $zlt_devicestudy->field($field)->where($query)->group('devicestudy_code')->select();
		$result['total'] = count($rs);
		
		response(ERRNO_SUCCESS, 'success', json_encode($result));
	}
}

?>
