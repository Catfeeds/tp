<?php

namespace Home\Controller;

use Think\Controller;

class ConvertController extends Controller {
    function Convert() {
        require_once(PUB_PATH . 'GPS/GPS.php');
        $from = get_param('from', null);
        $to = get_param('to', null);
        $lat = get_param('lat', null);
        $lng = get_param('lng', null);

        if ($from == 'gcj02' && $to == 'wgs84') {
            $gps = new GPS();
            response(0, 'success', json_encode($gps->gcj_decrypt($lat, $lng)));
        }
        response(-1, 'not support');
    }
}
