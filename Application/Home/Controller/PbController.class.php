<?php

namespace Home\Controller;

use Think\Controller;

class PbController extends Controller {

    //获取手表通信录OK
    function GetDevicePB() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $zlt_bind = M("zlt_bind");
        $bind = $zlt_bind->where("bind_user='$user[user_id]' and bind_device='$device[device_id]' and bind_valid=1")->find();
        if (!$bind) {
            response(ERRNO_DEVICE_PRIVILEGE_ERROR, "device privilege error");
        }
        $zlt_devicepb = M("zlt_devicepb");
        $pb = $zlt_devicepb->where("devicepb_device=$device[device_id]")->getField('devicepb_id as `index`, devicepb_name as `name`, devicepb_phone as `number`');
        foreach ($pb as $item) {
            $data[] = json_encode($item);
        }
        response(ERRNO_SUCCESS, "success", $data);
    }

    function addPb() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $number = get_param('number', null);
        $name = get_param('name', null);

        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user['user_id'], $device['device_id']);

        $zlt_devicepb = M("zlt_devicepb");
        $count = $zlt_devicepb->where("devicepb_device=$device[device_id]")->count();
        if ($count >= 10) {
            response(EXCEED_MAX_SIZE, "exceed max size");
        }

        $data["devicepb_device"] = $device['device_id'];
        $data["devicepb_phone"] = $number;
        $data["devicepb_name"] = $name;

        $id = $zlt_devicepb->data($data)->add();
        tail_device($imei,"phonebook");
        response(ERRNO_SUCCESS, "success");
    }

    function delPb() {
        $index = get_param('index', null);
        $token = get_param('token', null);
        $imei = get_param('imei', null);

        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user['user_id'], $device['device_id']);
        $zlt_devicepb = M("zlt_devicepb");
        $rel = $zlt_devicepb->where("devicepb_id=$index")->delete();
        tail_device($imei,"phonebook");
        response(ERRNO_SUCCESS, "success");
    }

    function editPb() {
        $index = get_param('index', null);
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $number = get_param('number', null);
        $name = get_param('name', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user['user_id'], $device['device_id']);
        $zlt_devicepb = M("zlt_devicepb");
        $data["devicepb_device"] = $device['device_id'];
        $data["devicepb_phone"] = $number;
        $data["devicepb_name"] = $name;
        $rel = $zlt_devicepb->where("devicepb_id=$index")->data($data)->save();
        tail_device($imei,"phonebook");
        response(ERRNO_SUCCESS, "success");
    }

    //	//设置手表通信录
    function SetDevicePB() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $indexs = get_param('indexes', null);
        $numbers = get_param('numbers', null);
        $names = get_param('names', null);
        $zlt_devicepb = M("zlt_devicepb");
        //删除功能
        if ($indexs != "" && $numbers == "" && $names == "") {
            $id = $zlt_devicepb->where("devicepb_id=$indexs")->delete();
			tail_device($imei,"phonebook");
            response(ERRNO_SUCCESS, "success");
        }
        $device = check_device($imei);

        $data = $this->buildIndex($indexs, $numbers, $names);
        $count = $this->count_index($data, $device['device_id']);

        if ($count + $ni > 10) {
            response(ERRNO_FAIL, "exceed max size");
        }
        $user = check_user_by_usertoken($token);

        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user['user_id'], $device['device_id']);

        //向手表通讯录写入数据
        foreach ($data as $key => $val) {
            $count_device_pb_count = $zlt_devicepb->where("devicepb_id='$key' and devicepb_device='$device[device_id]'")->count();

            if ($count_device_pb_count > 0) {
                $this->pb_update($val, $device['device_id']);
                $ids[] = $key;
            }
            if ($count_device_pb_count == 0) {
                $ids[] = $this->pb_insert($val, $device['device_id']);
            }
            if ($key == "") {
                $this->pb_update($val, $device['device_id']);
                $ids[] = $key;
            }
        }
        //开始返回数据
        $pbs = $zlt_devicepb->where("devicepb_device='$device[device_id]'")->select();
        foreach ($pbs as $pb) {
            $pba[] = iconv("utf-8", "gb2312//IGNORE", $pb[devicepb_name]);
            $pba[] = $pb[devicepb_phone];
        }
        tail_device($imei,"phonebook");
        response(ERRNO_SUCCESS, "success", array_filter($ids));
    }

    //重组索引
    function buildIndex($indexs, $names, $numbers) {

        $indexs = split(',', $indexs);
        $names = split(',', $names);
        $numbers = split(',', $numbers);

        if (count($indexs) != count($numbers) || count($indexs) != count($names)) {
            response(ERRNO_ARGUMENTS_ERROR, 'params size not match');
        }

        $count = count($indexs);
        for ($i = 0; $i < $count; $i++) {
            if ($numbers[$i] != "") {
                $data[$indexs[$i]] = $indexs[$i] . "," . $names[$i] . "," . $numbers[$i];
            }
        }
        if ($data) {
            return $data;
        } else {
            response(ERRNO_ARGUMENTS_ERROR, 'no data');
        }
    }

    //统计手表电话记录 大于10退出
    function count_index($indexs, $device_id) {
        $zlt_devicepb = M("zlt_devicepb");
        $n = 0;
        $count = $zlt_devicepb->where("devicepb_device='$device_id'")->count();
        foreach ($indexs as $key => $val) {
            $pb = $zlt_devicepb->where("devicepb_id='$key'")->find();
            if (!$pb) {
                $n = $n + 1;
            }
        }
        return $n + $count;
    }

    //写入数据到表
    function pb_insert($data, $device_id) {
        $val = explode(",", $data);
        $data1["devicepb_device"] = $device_id;
        $data1["devicepb_phone"] = $val[1];
        $data1["devicepb_name"] = $val[2];
        $zlt_devicepb = M("zlt_devicepb");
        $id = $zlt_devicepb->data($data1)->add();
        return $id;
    }

    //更新表数据
    function pb_update($data, $device_id) {
        $val = explode(",", $data);
        $where["devicepb_id"] = $val[0];
        $where["devicepb_device"] = $device_id;
        $data1["devicepb_phone"] = $val[2];
        $data1["devicepb_name"] = $val[1];

        $zlt_devicepb = M("zlt_devicepb");
        $id = $zlt_devicepb->where($where)->data($data1)->save();

        return $id;
    }

}
