<?php

namespace Home\Controller;

use Think\Controller;

class BindController extends Controller {

    // 绑定用户与设备关系
    function Bind() {
        //接收参数
        $token = get_param('token1', null);
        $apptoken = get_param('token2', null);
        $imei = get_param('imei', null);
        $nick = get_param('nick', null);
        //检查app
        $app = check_app_by_apptoken($apptoken);
        //检查用户
        $user = check_user_by_usertoken($token);
        //检查设备
        $device = check_device($imei);
        //if ($app["app_id"] != $user["user_app"]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error!");
        //}
        //检查绑定状态
        $bind = $this->bind_stauts($user, $device);
        /** 设备被绑定情况* */
        if ($bind["bind_valid"] == 1 && $device['device_master'] != 0) {
            //response(1, "device already bonded!");
            response(1, "您已绑定此设备!");
        }
        /**         * 第一次绑定情况* */
        if ($device["device_master"] == 0 && empty($bind)) {
            //插入绑定表数据
            $zlt_bind_data['bind_device'] = $device["device_id"];
            $zlt_bind_data['bind_user'] = $user["user_id"];
            $zlt_bind_data['bind_nick'] = $nick;
            $zlt_bind_data['bind_valid'] = 1;
            $this->bind_data_insert($zlt_bind_data);
            //设备管理员更改
            $data_d["device_app"] = $app["app_id"];
            $data_d["device_master"] = $user["user_id"];
            $zlt_device = M("zlt_device");
            $zlt_device->where("device_id='$device[device_id]'")->data($data_d)->save();
            tail_device($imei, "family", 0);
            response(ERRNO_SUCCESS, "success");
        }
        /** 申请绑定情况* */
        if ($device["device_master"] != 0) {
            if (empty($bind)) {
                $zlt_bind_data['bind_device'] = $device["device_id"];
                $zlt_bind_data['bind_user'] = $user["user_id"];
                $zlt_bind_data['bind_nick'] = $nick;
                $zlt_bind_data['bind_time'] = date('Y-m-d H:i:S');
                $zlt_bind_data['bind_valid'] = 0;
                $this->bind_data_insert($zlt_bind_data);
            }
            response(ERRNO_AUTHORIZATION_REQUIRE, "authorization require");
        }
        /*     * 设备管理员没有确认绑定，第2次申请绑定情况* */
        if ($device["device_master"] != 0 && !empty($bind) && $bind['bind_valid'] == 0) {
            response(ERRNO_WAITING_REVIEW, "ERRNO_WAITING_REVIEW");
        }
        $zlt_bind = M("zlt_bind");
        $query = 'user_id, user_name, bind_nick, bind_time';
        $conditions["bind_device"] = $device["device_id"];
        $conditions["bind_valid"] = 1;
        $users = $zlt_bind->where($conditions)->field($query)->join("zlt_user ON zlt_bind.bind_user = zlt_user.user_id")->select();
        foreach ($users as $user) {
            $data[] = iconv("utf-8", "gb2312//IGNORE", $user["bind_nick"]);
            $data[] = $user["user_name"];
        }
        tail_device($imei, "family", 0);
        response(ERRNO_SUCCESS, "success");
    }

    //绑定状态
    private function bind_stauts($user, $device) {
        $zlt_bind = M("zlt_bind");
        $bind = $zlt_bind->where("bind_user='$user[user_id]' and bind_device='$device[device_id]'")->find();
        return $bind;
    }

    //更改绑定表数据
    private function bind_data_update($data, $bind_id) {
        if ($data) {
            $zlt_bind_data = M('zlt_bind');
            $zlt_bind_data->where("bind_id='$bind_id'")->save($data);
        }
    }

    //写入绑定表数据
    private function bind_data_insert($data) {
        if ($data) {
            $zlt_bind_data = M('zlt_bind');
            $zlt_bind_data->add($data);
        }
    }

    //转移管理员给指定的用户ok
    function BindMaster() {
        $token = get_param('token', null); //用户token
        $newmaster = get_param('user', null); //这里bindmaster是指新的用户id
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);

        $zlt_bind = M("zlt_bind");
        $bind = $zlt_bind->where("bind_user='$newmaster' and bind_device='$device[device_id]' and bind_valid=1")->find();
        if (!$bind) {
            response(ERRNO_FAIL, "not bonded user!");
        }
        if ($device[device_master] != $user[user_id]) {
            response(ERRNO_AUTHORIZATION_REQUIRE, "authorization error");
        }

        $zlt_device = M("zlt_device");
        $data['device_master'] = $newmaster;
        $zlt_device->where("device_id='$device[device_id]'")->save($data);
        response(ERRNO_SUCCESS, "success");
    }

    //一个用户绑定了那些设备
    function GetBonds() {
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $zlt_bind = M("zlt_bind");
        $map['bind_user'] = $user["user_id"];
        $map["bind_valid"] = 1;
        $field = "device_imei as imei,device_name as name,device_phone as phone";
        $jion = "zlt_device on `zlt_bind`.bind_device = `zlt_device`.device_id";
        $binds = $zlt_bind->where($map)->join($jion)->field($field)->select();
        $imeis = array();
        foreach ($binds as $bind) {
            $imeis[] = json_encode($bind);
        }
        response(ERRNO_SUCCESS, "success", $imeis);
    }

    //获取设备绑定了那些用户（也可以包含自己）
    function getbondedusers() {
        //用户检查是否存在
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        if ($device["device_master"] != $user["user_id"]) {
            response(ERRNO_AUTHORIZATION_REQUIRE, "authorization error");
        }
        $zlt_bind = M("zlt_bind");
        $map['bind_device'] = $device["device_id"];
        $map["bind_valid"] = 1;
        $field = "user_id as id, user_name as name,bind_nick as nick";
        $jion = "zlt_user on `zlt_bind`.bind_user = `zlt_user`.user_id";
        $binds = $zlt_bind->where($map)->join($jion)->field($field)->select();
        response(ERRNO_SUCCESS, "success", json_encode($binds));
    }

    //关联用户到jpush
    function rel_jpush() {
        require_once (PUB_PATH.'/lib/jpush.php');
        $token = get_param('token', null);
        $jpushid = get_param('jpushid', null);
        $user = check_user_by_usertoken($token);
        try {
        	$jpushClient = jpush_client($user['token']['usertoken_app']);
        	jpush_alias($jpushClient, $jpushid, $user['user_id']);
        	
            $zlt_jpush = M("zlt_jpush");
            $jpush = $zlt_jpush->where("jpush_regid='$jpushid' and jpush_user='$user[user_id]'")->find();
            if ($jpush) {
                response(ERRNO_SUCCESS, "success");
            }
            $jpush["jpush_regid"] = $jpushid;
            $jpush["jpush_user"] = $user['user_id'];
            $zlt_jpush->add($jpush);
        } catch (\Exception $e) {

            response(ERRNO_FAIL, "jpush alias failed(" . $e->getMessage() . ")");
        }
        // $data[user_wgid] = $jpushid;
        // $user->where("user_id ='$user[user_id]'")->data($data)->save();
        response(ERRNO_SUCCESS, "success");
    }

    //回应绑定请求
    function bindRsp() {
        require_once (PUB_PATH.'/lib/jpush.php');
        $token = get_param('token1', null);
        $apptoken = get_param('token2', null);

        $imei = get_param('imei', null);
        $userreq = get_param('user', null); //请求绑定的用户id
        $result = get_param('result', null); //审核结果：0:拒绝绑定，1：允许绑定
        $zlt_apptoken = M("zlt_apptoken");
        $appid = $zlt_apptoken->where("apptoken_token='$apptoken'")->find();
        if (!$appid) {
            response(ERRNO_APP_ERROR, "apptoken error!");
        }
        $zlt_app = M("zlt_app");
        $app = $zlt_app->where("app_id ='$appid[apptoken_app]'")->find();
        if (!$app) {
            response(ERRNO_APP_ERROR, "app error!");
        }
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $zlt_bind = M('zlt_bind');
        $bind = $zlt_bind->where("bind_user='$userreq' and bind_device='$device[device_id]' and bind_valid=1")->find();
        if ($bind) {
            response(ERRNO_SUCCESS, "device already bonded!");
        }
        if ($result == 1) {
            $zlt_bind = M("zlt_bind");
            $bind = $zlt_bind->where("bind_user = '$userreq' and bind_device ='$device[device_id]'")->find();
            if ($bind) {
                $where['bind_device'] = $device[device_id];
                $where['bind_user'] = $userreq;
                $data["bind_valid"] = 1;
                $zlt_bind->where($where)->data($data)->save();
            }
            tail_device($imei, "family", 0);
        }

        try {
        	$zlt_user = M('zlt_user');
        	
        	$userreq_user = $zlt_user->where("user_id=$userreq")->find();
        	if( !$userreq_user ){
        		response(ERRNO_USER_ERROR, "user error!");
        	}
        	
        	$zlt_usertoken = M('zlt_usertoken');
        	$userreq_user['token'] = $zlt_usertoken->where("usertoken_user=$userreq_user[user_id]")->order('usertoken_tokentime desc')->find();
        	
            $title = '绑定回复信息';
            $extras = array('type' => 4, 'device_name' => $device['device_name'], 'imei' => $imei, 'result' => $result, 'time' => time());
                        
            $jpushClient = jpush_client($userreq_user['token']['usertoken_app']);
            
            $zlt_jpush = M('zlt_jpush');
            if( $zlt_jpush->where("jpush_user=$userreq")->find()){
            	jpush_push($jpushClient, $userreq, $imei, $title, $extras, $device);
            }
            
        } catch (\Exception $e) {
            response(ERRNO_FAIL, $e->getMessage());
        }
        response(ERRNO_SUCCESS, "success");
    }

    function bindreq() {
    	require_once (PUB_PATH.'/lib/jpush.php');
    	
        $token = get_param('token1', null);
        $apptoken = get_param('token2', null);
        $imei = get_param('imei', null);
        $msg = get_param('msg', null); //请求说明信息

        $zlt_apptoken = M("zlt_apptoken");
        $appid = $zlt_apptoken->where("apptoken_token='$apptoken'")->find();
        if (!$appid) {
            response(ERRNO_APP_ERROR, "apptoken error!");
        }
        $zlt_app = M("zlt_app");
        $app = $zlt_app->where("app_id ='$appid[apptoken_app]'")->find();
        if (!$app) {
            response(ERRNO_APP_ERROR, "app error!");
        }
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        try {
        	$zlt_user = M('zlt_user');
        	
        	$master_user = $zlt_user->where("user_id=$device[device_master]")->find();
        	if( !$master_user ){
        		response(ERRNO_DEVICE_ERROR, "device error!");
        	}
        	
        	$zlt_usertoken = M('zlt_usertoken');
        	$master_user['token'] = $zlt_usertoken->where("usertoken_user=$master_user[user_id]")->order('usertoken_tokentime desc')->find();
        	
        	$jpushClient = jpush_client($master_user['token']['usertoken_app']);
        	        	
            $title = '绑定请求信息';
            $extras = array('type' => 3, 'device_name' => $device['device_name'], 'imei' => $imei, 'user_name' => $user['user_name'], 'user' => $user["user_id"], 'msg' => $msg, 'time' => time());
            
            $zlt_jpush = M('zlt_jpush');
            if( $zlt_jpush->where("jpush_user=$master_user[user_id]")->find()){
            	jpush_push($jpushClient, $master_user['user_id'], $imei, $title, $extras, $device);   
            }
        } catch (\Exception $e) {
            response(ERRNO_FAIL, $e->getMessage());
        }
        response(ERRNO_SUCCESS, "success");
    }

    //解除绑定
    function unbind() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user["user_id"], $device["device_id"]);

        $zlt_bind = M("zlt_bind");
        if ($device['device_master'] == $user['user_id']) {
            $map["bind_device"] = $device["device_id"];
            $map["bind_valid"] = 1;
            $bonds = $zlt_bind->where($map)->count();
            if ($bonds > 1) {
                response(ERRNO_AUTHORIZATION_MASTER, "master error!");
            }
            $data["device_master"] = 0;
            $where["device_id"] = $device["device_id"];
            $zlt_device = M("zlt_device");
            $zlt_device->where($where)->save($data);
        }
        $zlt_bind->where("bind_id='$bind[bind_id]'")->delete();
		tail_device($imei, "family");
        response(ERRNO_SUCCESS, "success");
    }

    //通过imei获取获取设备信息     
    function getdevicebyimei() {
        $imei = get_param('imei', null);
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $list = array();
        foreach ($device as $ls) {
            $data[imei] = $ls[device_imei];
            $data[name] = $ls[device_name];
            $data[phone] = $ls[device_phone];
            $list[] = json_encode($data);
        }
        response(ERRNO_SUCCESS, "success", $list);
    }

    //解除绑定关系
    function jeichuBind() {
        $imei = get_param('imei', null);
        $device = check_device($imei);
        $zlt_bind = M("zlt_bind");
        $zlt_device = M("zlt_device");
        $re = $zlt_bind->where("bind_device=$device[device_id]")->delete();
        if ($re === true) {
            $data['device_master'] = 0;
            $where['device_imei'] = $imei;
            $zlt_device->where($where)->data($data)->save();
        }
    }

}
