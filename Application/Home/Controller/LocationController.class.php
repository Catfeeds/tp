<?php

namespace Home\Controller;

use Think\Controller;

class LocationController extends Controller {

    function get_address($lng, $lat) {
        $key = "8668f6085cb8575d7c340bd77040ab0b";
        $url = "http://restapi.amap.com/v3/geocode/regeo?key=" . $key . "&location=" . $lng . "," . $lat;

        $ch = curl_init();
        //参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $str = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($str, true);
        $address = $result['regeocode']['formatted_address'];
        if( is_array($address) ){
        	if( empty($address) ){
        		return "";
        	}
        	return $address[0];
        }
        return $address;
    }

   //获取最近一次定位的位置
    function Location() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $coordtype = get_param('coordtype', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user["user_app"] != $device["device_app"]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user["user_id"], $device["device_id"]);
        $zlt_location_rt = M("zlt_location_rt");
        $field = "location_id as id,location_address as address,location_time as time,location_lati as lat,location_longi as lng,location_accuracy as accuracy,location_speed as speed,location_course as bearing";
        $location = $zlt_location_rt->field($field)->where("location_device = $device[device_id]")->order('location_id desc')->limit(1)->find();
        if (!$location) {
            response(ERRNO_LOCATION_ERROR, "location data not found");
        }
        if ($coordtype == 'gcj02') {
            $locs[] = $location[lng] . "," . $location[lat];
            $coord = array(convert_coords($locs));
        }
        $location["address"] = $this->get_address($location["lng"], $location["lat"]);
        $status = json_decode(get_device_status($imei), true);
        $location["lat"] = $coord[0][0][lat];
        $location["lng"] = $coord[0][0][lng];
        if ($status['data']['active'] == NULL) {
            $location["online"] = 0;
        } else {
            $location["online"] = $status['data']['active'];
        }
        //if ($location["power"] == NULL) {
        if ($status['data']['power'] == NULL) {
            $location["power"] = 0;
        } else {
            $location["power"] = $status['data']['power'];
        }
        $location["time"] = strtotime($location['time']);
        response(ERRNO_SUCCESS, "success", json_encode($location));
    }

    //设备状态
    function online($imei) {
        require_once(PUB_PATH . 'device_status_cache/status.php');
        return device_status($imei);
    }

    function power($imei) {
        require_once(PUB_PATH . 'device_status_cache/status.php');
        return device_power($imei);
    }

    function history_fast() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $begin = get_param('begin', null);
        $end = get_param('end', null);
        $coordtype = get_param('coordtype', '');
        $begin = date("Y-m-d H:i:S", $begin);
        $end = date("Y-m-d H:i:S", $end);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        $bind = check_bind($user['user_id'], $device['device_id']);
        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $zlt_location = M("zlt_location");
        $field = "location_id,location_address as address,location_time,location_lati as lat,location_longi as lng,location_speed as speed,location_course as bearing,location_cellid as cellid";
        $locations = $zlt_location->field($field)->where("location_device = '$device[device_id]' and location_time>='$begin' and location_time<'$end'")->order('location_time desc')->select();
        if ($coordtype == 'gcj02') {
            require_once(PUB_PATH . 'convert_svervice/location_convert.php');
            $locations = convert(arrayToObject($locations));
        }
        $data = array();
        $data1 = array();
        /*foreach ($this->object2array($locations) as $loc) {
            if ($loc[lat] == 0 || $loc[lng] == 0) {
                continue;
            }
            $data[] = json_encode($loc);
        }*/
		//过滤掉定位跑偏的点
        $data1=$this->object2array($locations);
		$len = count($data1);
		if(count($data1)>3){
			for ($i = 0; $i<$len;$i++){
				if($i < $len-2 && (abs($data1[$i][lat]-$data1[$i+2][lat])+abs($data1[$i][lng]-$data1[$i+2][lng])) < 0.011&&(abs($data1[$i][lat]-$data1[$i+2][lat])+abs($data1[$i][lng]-$data1[$i+2][lng])) < (abs($data1[$i][lat]-$data1[$i+1][lat])+abs($data1[$i][lng]-$data1[$i+1][lng]))){
					$data[] = json_encode($data1[$i]);
					$data[] = json_encode($data1[$i+2]);
					$i++;
				}else{
					$data[] = json_encode($data1[$i]);
				}
			}
		}else{
			foreach ($this->object2array($locations) as $loc) {
				if ($loc[lat] == 0 || $loc[lng] == 0) {
					continue;
				}
				$data[] = json_encode($loc);
			}
		}
	    /*$count=0;
        foreach ($this->object2array($locations) as $key => $loc) {
            if($loc[cellid] !=0){
                $count++;
                if($count == 3){
                    $data[]=json_encode($locations[$key-1]);
                    $count=0;
                }
            }else{
                $data[] = json_encode($loc);
                $count = 0;
            }
        }*/
		//过滤掉基站定位跑偏的点
		/*$count=0;
        $n = 0;
        foreach ($this->object2array($locations) as $key => $loc) {
            if($loc[cellid] !=0){
                $count++;
                $data1[] = $loc;
                if($count == 3){
                    $n = count($data1);
                    for ($j = 0; $j < $n - 1; $j++)
                    {
                        for ($i = 0; $i < $n - 1 - $j; $i++)
                        {
                            if ($data1[$i][lng]+$data1[$i][lat] > $data1[$i+1][lng]+$data1[$i+1][lat])
                            {
                                $temp = $data1[$i];
                                $data1[$i] = $data1[$i+1];
                                $data1[$i+1] = $temp;
                            }
                        }
                    }
                    $data[] = json_encode($data1[1]);
                    $data1= array();
                    $n =0;
                    $count=0;
                }
            }else{
                $data[] = json_encode($loc);
                $n = 0;
                $data1= array();
                $count = 0;
            }
        }*/
        response(ERRNO_SUCCESS, "success", $data);
    }

    function object2array($object) {
        $object = json_decode(json_encode($object), true);
        return $object;
    }

    //实时定位
    function getloction() {
    	set_time_limit(120);
        $imei = get_param('imei', null);
        $token = get_param('token', null);
        $coordtype = get_param('coordtype', 'gcj02');
        
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        
        $result = tail_device($imei, "loc", 1, 120);
        
        if( !$result ){
        	response(ERRNO_OFFLINE, "device offline");
        }
        
        $obj = json_decode($result);
        
        if( !$obj ){
        	response(ERRNO_OFFLINE, "device offline");
        }
        
        if( $obj->errcode == 0 ){
	        if ($coordtype == 'gcj02') {
	        	$locs[] = $obj->data->lng . "," . $obj->data->lat;
	        	$coord = array(convert_coords($locs));
	        	$obj->data->lng = $coord[0][0][lng];
	        	$obj->data->lat = $coord[0][0][lat];
	        }
	        $obj->data->address = $this->get_address($obj->data->lng, $obj->data->lat);
        }
        echo json_encode($obj);
    }

}

?>
