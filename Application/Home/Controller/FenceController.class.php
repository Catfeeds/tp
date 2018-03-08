<?php

namespace Home\Controller;

use Think\Controller;

class FenceController extends Controller {
    //添加围栏
    function fence_add() {
        $token = get_param('token', null);
        $imeistr = get_param('imei', null);
        $name = get_param('name', null);
        $type = get_param('type', null);
        $lng1 = get_param('lng1', null);
        $lat1 = get_param('lat1', null);
        $address = get_param('address', null);
        $lng2 = 0;
        $lat2 = 0;
        if ($type == 'rect') {
            $lng2 = get_param('lng2', null);
            $lat2 = get_param('lat2', null);
            $type = 1;
        } else {
            $lng2 = get_param('radius', null);
            $type = 0;
        }
        //验证用户
        $user = check_user_by_usertoken($token);
        $zlt_fine = M("zlt_fine");
        $fine[fine_device] = 0;
        $fine[fine_user] = $user[user_id];
        $fine[fine_name] = $name;
        $fine[fine_type] = $type;
        $fine[fine_lng1] = $lng1;
        $fine[fine_lat1] = $lat1;
        $fine[fine_lng2] = $lng2;
        $fine[fine_lat2] = $lat2;
        $fine[fine_address] = $address;
        $fine_id = $zlt_fine->add($fine);

        $imeis = split(",", $imeistr);
        foreach ($imeis as $imei) {
            //检查是否有这个设备
            $zlt_device = M("zlt_device");
            $device = $zlt_device->where("device_imei=$imei")->find();
            //检查是不是有绑定
            $bind = $this->check_exit_bind($user['user_id'], $device['device_id']);

            if ($device && $bind) {
                $zlt_fine_map = M("zlt_fine_map");
                $data["fine_map_fine"] = $fine_id;
                $data["fine_map_device"] = $device["device_id"];
                $zlt_fine_map->data($data)->add();
            }
        }
        response(ERRNO_SUCCESS, "success", json_encode(array("id" => $fine_id)));
    }

    //检查是否有绑定
    function check_exit_bind($user_id, $device_id) {
        $zlt_bind = M("zlt_bind");
        $bind = $zlt_bind->where("bind_user='$user_id' and bind_device='$device_id' and bind_valid=1")->find();
        return $bind;
    }

    //更新围栏
    function fence_update() {
        $token = get_param('token', null);
        $imeistr = get_param('imei', null);
        $name = get_param('name', null);
        $id = get_param("id", null);
        $type = get_param('type', null); //围栏形状,circle:圆形，rect:方形
        $address = get_param('address', null);
        $lng1 = get_param('lng1', null);
        $lat1 = get_param('lat1', null);

        if ($type == 'rect') {
            $lng2 = get_param('lng2', null);
            $lat2 = get_param('lat2', null);
            $type = 1;
        } else {
            $lng2 = get_param('radius', null);
            $type = 0;
        }
        $user = check_user_by_usertoken($token);

        $zlt_fine = M("zlt_fine");
        $fine = $zlt_fine->where("fine_id='$id'")->find();

        if (!fine) {
            response(ERRNO_FAIL, "invalid fence id");
        }

        $data_fine[fine_device] = 0;
        $data_fine[fine_user] = $user[user_id];
        $data_fine[fine_name] = $name;
        $data_fine[fine_type] = $type;
        $data_fine[fine_lng1] = $lng1;
        $data_fine[fine_lat1] = $lat1;
        $data_fine[fine_lng2] = $lng2;
        $data_fine[fine_lat2] = $lat2;
        $data_fine[fine_address] = $address;
        $zlt_fine->where("fine_id='$id'")->save($data_fine);

        
        $str = explode(",", $imeistr);
        $zlt_device = M("zlt_device");
        $zlt_fine_map = M("zlt_fine_map");
        //清空围栏绑定表
        $zlt_fine_map->where("fine_map_fine='$id'")->delete();
        foreach ($str as $v) {
            $device = $zlt_device->where("device_imei=$v")->find();
            if (empty($device)) {
                exit();
            }
            $fine['fine_map_fine'] = $id;
            $fine['fine_map_device'] = $device['device_id'];
            //添加围栏对设备的绑定
            $rel = $zlt_fine_map->where($fine)->find();
            if (empty($rel)) {
                $zlt_fine_map->data($fine)->add();
            }
        }
        response(ERRNO_SUCCESS, "success", json_encode(array("id" => $id)));
    }

    //删除围栏
    function fence_del() {
        $token = get_param('token', null);
        $id = get_param('id', null);
        $user = check_user_by_usertoken($token);

        $zlt_fine = M("zlt_fine");
        $fine = $zlt_fine->where("fine_id=$id")->find();
        if (!$fine) {
            response(ERRNO_FAIL, "other error");
        }

        $zlt_fine_map = M("zlt_fine_map");
        $finemaps = $zlt_fine_map->where("fine_map_fine = $fine[fine_id]")->find();

        foreach ($finemaps as $finemap) {
            $zlt_fine_map->where("fine_map_id=$finemaps[fine_map_id]")->delete();
        }

        $zlt_fine->where("fine_id=$id")->delete();
        response(ERRNO_SUCCESS, "success");
    }

    //得到围栏列表
    function fence_get_list() {
        $token = get_param('token', null);
        $last = get_param('last', 0);
        $user = check_user_by_usertoken($token);

        $field = 'fine_id as id,fine_name as name,fine_type as type,fine_lng1 as lng1,fine_lat1 as lat1,fine_lng2 as lng2,fine_lat2 as lat2,fine_address';
        $zlt_fine = M("zlt_fine");
        $fines = $zlt_fine->field($field)->where("fine_user='$user[user_id]' and fine_enabled=1")->select();
        $json = array();
        $zlt_fine_map = M("zlt_fine_map");
        foreach ($fines as $fine) {
            $data[id] = $fine[id];
            $data[name] = $fine[name];
            $data[type] = $fine[type];
            $loc1 = array($fine[lng1] . "," . $fine[lat1]);
            $loc2 = array($fine[lng2] . "," . $fine[lat2]);
            $rel1 = convert_coords($loc1);
            $rel2 = convert_coords($loc2);
            $data[lng1] = $rel1[0][lng];
            $data[lat1] = $rel1[0][lat];
            $data[lng2] = $rel2[0][lng];
            $data[lat2] = $rel2[0][lat];
            $data[devices] = $this->devices($fine[id]);
            $data[address] = $fine[fine_address];
//            $data[devices] = $this->onback_device($fine[id]);
            $data[radius] = $this->onback_radius($fine[lng2]);
            $json[] = json_encode($data);
        }
        response(ERRNO_SUCCESS, "success", $json);
    }

    function devices($fine_id) {
        $zlt_fine_map = M("zlt_fine_map");
        $devices = $zlt_fine_map->field('device_imei')->join('zlt_device ON zlt_fine_map.fine_map_device = zlt_device.device_id')->where('fine_map_fine='."$fine_id")->select();

        $imeis = [];
        foreach ($devices as $device) {
            $imeis[] = $device['device_imei'];
        }
        return $imeis;
    }

    function radius() {
        return $this->lng2;
    }

    //回调
    function onback_device($id) {
        $zlt_fine_map = M("zlt_fine_map");
        $result = $zlt_fine_map->field('device_imei as imei')->where("fine_map_fine=$id")->join("zlt_device ON zlt_fine_map.fine_map_device=zlt_device.device_id ")->find();
        $device[] = $result[imei];
        return $device;
    }

    //回调
    function onback_radius($lng2) {
        return $lng2;
    }

}

?>