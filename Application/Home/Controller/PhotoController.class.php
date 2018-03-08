<?php

namespace Home\Controller;

use Think\Controller;

class PhotoController extends Controller {

    //得到设备最新拍照列表
    function get_photo_list() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user[user_id], $device[device_id]);

        $zlt_photo = M("zlt_photo");
        $photos = $zlt_photo->where("photo_device= $device[device_id]")->field("photo_create as time, photo_url as url")->select();
        $data = array();
        foreach ($photos as $photo) {
            $data[] = $photo;
        }
        response(ERRNO_SUCCESS, "success", json_encode($data));
    }

    //远程拍照
    function takephoto() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $sid = get_param('sid', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);

        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user[user_id], $device[device_id]);
        //设备是否在线
        $status = json_decode(get_device_status($imei), true);
        if (!$status['data']['active']) {
            response(ERRNO_OFFLINE, "device offline");
        }
        //连接服务器范例http://localhost:4000/device/<imei>/takephoto/<fromuser>/<sid>
        $res = photo($imei, $user['user_name'], $sid);
        
        if( $res['errcode'] != 0 ){
        	response(ERRNO_FAIL, "failed");
        }
        
        response(ERRNO_SUCCESS, "success");
    }
	    //删除照片
    /*function delete_photo() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $url = get_param('url', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user[user_id], $device[device_id]);
        $name = end(explode('/',parse_url($url)['path']));
        $delete=unlink($_SERVER['DOCUMENT_ROOT']."/tp/upload/".$name);
        if(!$delete){
            response(ERRNO_ARGUMENTS_ERROR, "file name error");
        }
        $zlt_photo = M("zlt_photo");
        $photos = $zlt_photo->where("photo_device= $device[device_id] and photo_url='$url'")->delete();
        response(ERRNO_SUCCESS, success);
    }*/
	function delete_photo() {
		$token = get_param('token', null);
		$imei = get_param('imei', null);
		$urls = get_param('url', null);
		$user = check_user_by_usertoken($token);
		$device = check_device($imei);
		$bind = check_bind($user[user_id], $device[device_id]);
		$zlt_photo = M("zlt_photo");
		$urls =explode(',',$urls);
		$data = array();
		foreach ($urls as $item){
			$name = end(explode('/',$item));
			$photos = $zlt_photo->where("photo_device= $device[device_id] and photo_url='$item'")->delete();
			if(!$photos){
				response(ERRNO_ARGUMENTS_ERROR, "delete photo error");
			}
			$delete=unlink($_SERVER['DOCUMENT_ROOT']."/tp/upload/".$name);
			if(!$delete){
				response(ERRNO_ARGUMENTS_ERROR, "file name error");
			}
		}
		response(ERRNO_SUCCESS, success);
    }
}
