<?php

namespace Home\Controller;

use Think\Controller;

class UploadController extends Controller {
    function upload_baidu_lbs() {
        $zlt_location_rt = M("zlt_location_rt");
        $locations = $zlt_location_rt->find();
        $fname = tempnam("./tmp", "data.csv");
        $handle = fopen($fname, "w");
        fwrite($handle, "title,latitude,longitude,coord_type\n");
        foreach ($locations as $location) {
            fwrite($handle, $location[location_device] . "," . $location[location_lati] . "," . $location[location_longi] . ",1\n");
        }
        fclose($handle);
        $url = "http://api.map.baidu.com/geodata/v3/poi/upload";
        echo "<script>alert('$url')</script>";
        $fields['ak'] = 'Cjur26nklaIuNiVPlGONunyO';
        $fields['geotable_id'] = '144118';
        $fields['poi_list'] = new CurlFile($fname);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $str = curl_exec($ch);
        echo "<script>alert('$str')</script>";
        curl_close($ch);
    }
    function upload_photo() {
        $imei = get_param('imei', null);
        $lat = get_param('lat', 0);
        $lng = get_param('lng', 0);

        if (!isset($_FILES["file"])) {
            response(-1, "file not found");
            exit();
        }
        $file = $_FILES["file"];
        $name = 'photo' . $imei . time() . $file["name"];
        if ($file["error"] > 0) {
            response(-2, "file error");
            exit();
        }
        move_uploaded_file($file["tmp_name"],  __ROOT__ . "/upload/" . $name);
        $device = check_device($imei);
        $photo = M("zlt_photo");
        $data[photo_device] = $device->device_id;
        $data[photo_url] =   $url = dirname('https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . "/" . $name;
        $data[photo_lat] = $lat;
        $data[photo_lng] = $lng;
        $photo->add($data);
        response(0, "success", json_encode(array('id' => $photo[photo_id], 'url' => $photo[photo_url])));
    }

    //上传文件
    function upload() {
        $token = get_param('token', null);

        $user = check_user_by_usertoken($token);
	if (!isset($_FILES["file"])) {
            response(ERRNO_FAIL, "file not found");
            exit();
        }
        if (!isset($_FILES["file"])) {
            response(ERRNO_FAIL, "file not found");
            exit();
        }
        $file = $_FILES["file"];

        $name = $user['user_name'] . '_' . rand(100000, 999999) . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($file["error"] > 0) {
            response(ERRNO_FAIL, "file error");
            exit();
        }
        move_uploaded_file($file["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/tp/upload/" . $name);
        $url = C('DOMAIN') . "tp/upload" . "/" . $name;

        response(ERRNO_SUCCESS, "success", json_encode(array('url' => $url)));
    }

}
