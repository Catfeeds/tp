<?php

namespace Home\Controller;

use Think\Controller;

class DeviceController extends Controller {

    function test() {
        $url = C('MQTT_HOST') . ":" . C('MQTT_PORT') . "/device/502151020009767/config";
        //$url = "192.168.1.233:4000/device/123456789000001/config";
        $result = build_http($url);
        print_r($result);
    }

    //添加设备
    function device_add() {
        $name = get_param('name', null);
        $imei = get_param('imei', null);
        $card = get_param('card', null);
        $type = get_param('type', null);
        $app = get_param('app', null);
        $owner = get_param('owner', null);

        //判断设备是否添加
        $zlt_device = M("zlt_device");
        $device = $zlt_device->where("device_imei='$imei'")->find();
        if ($device) {
            response(-1, "设备以及存在");
        }
        $data['device_imei'] = $imei;
        $data['device_name'] = $name;
        $data['device_card'] = $card;
        $data['device_type'] = $type;
        $data['device_app'] = $app;
        $data['device_owner'] = $owner;

        $id = $zlt_device->data($data)->add();
        response(0, "success", json_encode(array('id' => $id)));
    }

    /*
     * 更改设备
     */

    function device_update() {
        $id = get_param('id', null);
        $zlt_device = M("zlt_device");
        $device = $zlt_device->where("device_id=$id")->find();
        if (!$device) {
            response(-1, "device not found");
        }
        $data[device_name] = get_param('name', $device[device_name]);
        $data[device_card] = get_param('card', $device[device_card]);
        $data[device_type] = get_param('type', $device[device_type]);
        $data[device_app] = get_param('app', $device[device_app]);
        $data[device_owner] = get_param('owner', $device[device_owner]);
        $zlt_device->where("device_id=$id")->save($data);
        response(0, "success");
    }

    //删除设备
    function device_del() {
        $id = get_param('id', null);
        $zlt_device = M("zlt_device");
        $device = $zlt_device->where("device_id=$id")->find();
        if (!device) {
            response(-2, "device not found");
        }
        $data[device_enabled] = 0;
        $zlt_device->where("device_id=$id")->save($data);
        response(0, "success");
    }

    //设备列表
    function device_get_list() {
        $off = get_param('offset', 0);
        $lim = get_param('limit', 0);

        $where = "device_enabled=1";
        $field = 'zlt_device.*,zlt_devtype.devtype_name,zlt_app.app_title';
        $jion_devtype = 'zlt_devtype ON(`zlt_device`.device_type = `zlt_devtype`.devtype_id)';
        $jion_app = '`zlt_app` ON(`zlt_device`.device_app = `zlt_app`.app_id)';

        if ($lim) {
            $query['offset'] = $off;
            $query['limit'] = $lim;
        }
        $zlt_device = M("zlt_device");
        $devices = $zlt_device->where($where)->join($jion_app)->join($jion_devtype)->field($field)->limit($query['offset'], $query['limit'])->select();
        $total = $zlt_device->where($where)->join($jion_app)->join($jion_devtype)->field($field)->limit($query['offset'], $query['limit'])->count();
        foreach ($devices as $device) {
            $data[] = $device;
        }
        response(0, "success", "{\"total\":$total,\"list\":[" . json_encode($data) . "]}");
    }

    /*
     * 设置设备信息
     * 根据传入的数据更改
     */

    function device_set_info() {
        $usertoken = get_param('token', null);
        $imei = get_param('imei', null);
        $name = get_param('name', null);
        $phone = get_param('phone', null);
        $zlt_user = M("zlt_user");
        $user = check_user_by_usertoken($usertoken);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        //    response(-3, "app privilege error");
        //}

        $bind = check_bind($user[user_id], $device[device_id]);
        $modified = 0;
        $zlt_device = M("zlt_device");
        if ($name) {
            $data[device_name] = $name;
            $modified = 1;
        }
        if ($phone) {
            $data[device_phone] = $phone;
            $modified = 1;
        }
        if ($modified) {
            $zlt_device->where("device_id=$device[device_id]")->data($data)->save();
        }
        response(0, "success");
    }

    //得到设备配置信息
    function device_get_info() {
        $usertoken = get_param('token', null);
        $imei = get_param('imei', null);
        $zlt_user = M("zlt_user");
        $user = check_user_by_usertoken($usertoken);
        $device = check_device($imei);
        $zlt_device_config = M("zlt_device_config");
		$bind = check_bind($user[user_id], $device[device_id]);
        try {
            $config = $zlt_device_config->where("device_config_id='$device[device_id]'")->find();
        } catch (\Exception $e) {
            $data[device_config_id] = $device[device_id];
            $zlt_device_config->add($data);
        }
        //if ($user['user_app'] != $device['device_app']) {
        //    response(-3, "app privilege error");
        //}
        $bind = check_bind($user["user_id"], $device["device_id"]);
        response(0, "success", json_encode(array('name' => $device['device_name'], 'phone' => $device['device_phone'], 'interval' => $config['device_config_interval'],'mode' => $config['device_config_mode'])));
    }

    //设备配置
    /*function device_config() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $interval = get_param('interval', 60);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "app privilege error");
        //}
        $bind = check_bind($user[user_id], $device[device_id]);
        $zlt_device_config = M("zlt_device_config");
        $status = json_decode(get_device_status($imei), true);
        if ($status['data']['active'] == 0) {
            response(ERRNO_OFFLINE, "device offline");
        }
        $devicecfg = $zlt_device_config->where("device_config_id='$device[device_id]'")->find();
        if (empty($devicecfg)) {
            $devicecfg[device_config_id] = $device[device_id];
            $devicecfg[device_config_interval] = $interval;
            $zlt_device_config->data($devicecfg)->add();
        } else {
            $devicecfg[device_config_interval] = $interval;
            $zlt_device_config->where("device_config_id='$device[device_id]'")->save($devicecfg);
        }
        tail_device($imei, "config");
        response(ERRNO_SUCCESS, "success");
    }*/
	//设备配置
    function device_config() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $state = get_param('state', 1);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user[user_id], $device[device_id]);
        $zlt_device_config = M("zlt_device_config");
        $status = json_decode(get_device_status($imei), true);
        if ($status['data']['active'] == 0) {
            response(ERRNO_OFFLINE, "device offline");
        }
		if ($state !=0 && $state !=1 && $state !=2){
           response(-1, "state parameter error");
        }
        tail_device($imei, "mode/".$state);
        response(ERRNO_SUCCESS, "success");
    }

    function getconf() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $zlt_user = M("zlt_user");
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user["user_app"] != $device["device_app"]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user['user_id'], $device['device_id']);
        $zlt_device_config = M("zlt_device_config");
        $devicecfg = $zlt_device_config->WHERE("device_config_id=$device[device_id]")->FIND();
        if (!$devicecfg) {
            $data[device_config_id] = $device[device_id];
            $zlt_device_config->add($data);
        }
        $data = json_encode(array('interval' => $devicecfg[device_config_interval], 'imei' => $device[device_imei]));
        response(ERRNO_SUCCESS, "success", $data);
    }

    //设置设备免打扰
    function setdevicend() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $indexs = get_param('indexes', null);
        $begins = get_param('begins', null);
        $ends = get_param('ends', null);
        $repeats = get_param('repeats', null);

        $zlt_devicend = M("zlt_devicend");
        if ($repeats == "" && $begins == "00:00" && $ends = "00:00") {
            $zlt_devicend->where("devicend_id=$indexs")->delete();
            response(ERRNO_SUCCESS, "success");
        }
        $indexs = split(',', $indexs);
        $begins = split(',', $begins);
        $ends = split(',', $ends);
        $repeats = split(',', $repeats);
        if (count($indexs) != count($begins)) {
            response(ERRNO_ARGUMENTS_ERROR, 'params size not match');
        }

        $user = check_user_by_usertoken($token);

        $device = check_device($imei);

        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}

        $bind = check_bind($user['user_id'], $device['device_id']);
        for ($i = 0; $i < count($indexs); $i++) {
            $x = split(':', $begins[$i]);
            $begins[$i] = $x[0] * 60 + $x[1];
            $x = split(':', $ends[$i]);
            $ends[$i] = $x[0] * 60 + $x[1];
        }

        $where['devicend_device'] = $device['device_id'];
        $count = $zlt_devicend->where($where)->count();
        $ni = 0;
        /*         * 判断4个设置开始 */
        for ($i = 0; $i < count($indexs); $i++) {
            if (!$indexs[$i]) {
                $ni = $ni + 1;
            } else {
                $nd = $zlt_devicend->where("devicend_id='$indexs[$i]'")->find();
                if ($nd) {
                    $ni --;
                }
            }
        }
        if ($count + $ni > 4) {
            response(ERRNO_FAIL, "exceed max size");
        }

        /*         * 判断4个设置结束 */
        for ($i = 0; $i < count($indexs); $i++) {
            if ($indexs[$i]) {
                $nd = $zlt_devicend->where("devicend_id='$indexs[$i]'")->find();
                if ($nd) {
                    $data[devicend_begin] = $begins[$i];
                    $data[devicend_end] = $ends[$i];
                    $data[devicend_repeat] = $repeats[$i];
                    $zlt_devicend->where("devicend_id='$indexs[$i]'")->data($data)->save();
                    $ids[] = $indexs[$i];
                } else {
                    $ids[] = 0;
                }
            } else {
                $data['devicend_device'] = $device['device_id'];
                $data['devicend_begin'] = $begins[$i];
                $data['devicend_end'] = $ends[$i];
                $data['devicend_repeat'] = $repeats[$i];
                $zlt_devicend->add($data);
                $ids[] = $nd[devicend_id];
            }
        }
        tail_device($imei, "nodisturb");
        response(ERRNO_SUCCESS, "success", $ids);
    }

    //add 免打扰
    function addDevicend() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $begin = get_param('begin', null);
        $end = get_param('end', null);
        $repeat = get_param('repeat', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);

        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $begins = explode(":", $begin);
        $ends = explode(":", $end);
        $bind = check_bind($user['user_id'], $device['device_id']);
        $data['devicend_device'] = $device['device_id'];
        $data['devicend_begin'] = $begins[0] * 60 + $begins[1];
        $data['devicend_end'] = $ends[0] * 60 + $ends[1];
        $data['devicend_repeat'] = $repeat;
        $zlt_devicend = M("zlt_devicend");
        $ids = $zlt_devicend->add($data);
        tail_device($imei, "nodisturb");
        response(ERRNO_SUCCESS, "success", json_encode($ids));
    }

    //edit 免打扰
    function editDevicend() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $begin = get_param('begin', null);
        $end = get_param('end', null);
        $repeat = get_param('repeat', null);
        $index = get_param('index', null);
        $zlt_devicend = M("zlt_devicend");
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $begins = explode(":", $begin);
        $ends = explode(":", $end);
        $bind = check_bind($user['user_id'], $device['device_id']);
        $data['devicend_device'] = $device['device_id'];
        $data['devicend_begin'] = $begins[0] * 60 + $begins[1];
        $data['devicend_end'] = $ends[0] * 60 + $ends[1];
        $data['devicend_repeat'] = $repeat;
        $ids = $zlt_devicend->where("devicend_id=$index")->save($data);
        tail_device($imei, "nodisturb");
        response(ERRNO_SUCCESS, "success", json_encode($ids));
    }

    //del 免打扰
    function delDevicend() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $index = get_param('indexe', null);

        $zlt_devicend = M("zlt_devicend");
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $ids = $zlt_devicend->where("devicend_id=$index")->delete();
        tail_device($imei, "nodisturb");
        response(ERRNO_SUCCESS, "success", json_encode($ids));
    }

    //获取免打扰设置
    function getdevicend() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user['user_id'], $device['device_id']);
        $zlt_devicend = M("zlt_devicend");
        $field = "devicend_id as `index`,devicend_begin,devicend_end,devicend_repeat as `repeat`";
        $nd = $zlt_devicend->field($field)->where("devicend_device='$device[device_id]'")->select();
        $list = array();
        foreach ($nd as $nds) {
            $data['index'] = $nds['index'];
            $data['begin'] = '' . str_pad((int) ($nds['devicend_begin'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($nds['devicend_begin'] % 60, 2, '0', STR_PAD_LEFT);
            $data['end'] = '' . str_pad((int) ($nds['devicend_end'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($nds['devicend_end'] % 60, 2, '0', STR_PAD_LEFT);
            $data['repeat'] = $nds['repeat'];
            $list[] = json_encode($data);
        }
        response(ERRNO_SUCCESS, "success", $list);
    }

    //通过imei获取获取设备信息
    function getdevicebyimei() {
        $imei = get_param('imei', null);
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        foreach ($device as $ls) {
            $data[imei] = $ls[device_imei];
            $data[name] = $ls[device_name];
            $data[phone] = $ls[device_phone];
            $list[] = json_encode($data);
        }
        response(ERRNO_SUCCESS, "success", json_decode($list));
    }

    //找手表
    function seekdevice() {
        $imei = get_param('imei', null);
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        tail_device($imei, "find");
        response(ERRNO_SUCCESS, "success");
    }

    //远程关机
    function shutdown() {
        $imei = get_param('imei', null);
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        tail_device($imei, "shutdown");
        response(ERRNO_SUCCESS, "success");
    }

    //闹钟加
    function clockAdd() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $begin = get_param('begin', null);
        $about = get_param('about', null);
        $repeat = get_param('repeat', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $begins = explode(":", $begin);
        $bind = check_bind($user['user_id'], $device['device_id']);


        $data['clock_device'] = $device['device_id'];
        $data['clock_begin'] = $begins[0] * 60 + $begins[1];
        $data['clock_repeat'] = $repeat;
        $data['about'] = $about;
        $zlt_clock = M("zlt_clock");
		$count = $zlt_clock->where("clock_device=$device[device_id]")->count();
        if ($count >= 5) {
            response(EXCEED_MAX_SIZE, "exceed max size");
        }
        $ids = $zlt_clock->add($data);
        tail_device($imei, "alarm ");
        response(ERRNO_SUCCESS, "success", json_encode($ids));
    }

    //闹钟编辑
    function clockEdit() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $begin = get_param('begin', null);
        $about = get_param('about', null);
        $repeat = get_param('repeat', null);
        $index = get_param('index', null);
        $begins = explode(":", $begin);
        $zlt_clock = M("zlt_clock");
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $data['about'] = $about;
        $data['clock_device'] = $device['device_id'];
        $data['clock_begin'] = $begins[0] * 60 + $begins[1];
        $data['clock_repeat'] = $repeat;
        $ids = $zlt_clock->where("id=$index")->save($data);
        tail_device($imei, "alarm ");
        response(ERRNO_SUCCESS, "success", json_encode($ids));
    }

    //闹钟删除
    function clockDel() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $index = get_param('index', null);

        $zlt_clock = M("zlt_clock");
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $ids = $zlt_clock->where("id=$index")->delete();
        tail_device($imei, "alarm");
        response(ERRNO_SUCCESS, "success", json_encode($ids));
    }

    //闹钟列表
    function clockList() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user['user_app'] != $device['device_app']) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user['user_id'], $device['device_id']);
        $zlt_clock = M("zlt_clock");
        $field = "`about`,id as `index`,clock_begin,clock_end,clock_repeat as `repeat`";
        $nd = $zlt_clock->field($field)->where("clock_device='$device[device_id]'")->select();

        $list = array();
        foreach ($nd as $nds) {
            $data['index'] = $nds['index'];
            $data['about'] = $nds['about'];
            $data['begin'] = '' . str_pad((int) ($nds['clock_begin'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($nds['clock_begin'] % 60, 2, '0', STR_PAD_LEFT);
            $data['end'] = '' . str_pad((int) ($nds['clock_end'] / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad($nds['clock_end'] % 60, 2, '0', STR_PAD_LEFT);
            $data['repeat'] = $nds['repeat'];
            $list[] = json_encode($data);
        }
        response(ERRNO_SUCCESS, "success", $list);
    }

}

?>
