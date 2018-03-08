<?php

namespace Home\Controller;

use Think\Controller;

class EventController extends Controller {
    //处理消息 Ok
    function dealevents() {
        $token = get_param('token', null);
        $eventstr = get_param('events', null);
        $events = split(",", $eventstr);
        $user = check_user_by_usertoken($token);
        $data[event_status] = 1;
        $map[event_jpushid] = array('in', $events);
        $map[event_recipient] = $user[user_id];
        $zlt_event = M("zlt_event");
        $zlt_event->where($map)->save($data);
        response(ERRNO_SUCCESS, "success");
    }

    //获取最新消息0K
    function pullevents() {
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $zlt_event = M("zlt_event");
        $events = $zlt_event->field("event_id as id, event_jpushid as msg_id,event_extra")->where("event_recipient='$user[user_id]' and event_status=0")->select();
        $data = array();
        foreach ($events as $event) {
            $list['event_extra'] = json_decode($event['event_extra']);
            $list['id'] = json_decode($event['id']);
            $list['msg_id'] = json_decode($event['msg_id']);
            $data[] = json_encode($list);
        }
        response(ERRNO_SUCCESS, 'success', $data);
    }

}
