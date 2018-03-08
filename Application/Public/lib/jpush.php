<?php
require_once(PUB_PATH.'/vendor/jpush/autoload.php');

use JPush\Client as JPush;

function jpush_client($appid){
	$appcfg = M('zlt_appcfg');
	$cfg = $appcfg->field('appcfg_value')->where("appcfg_app=$appid AND appcfg_key='jpush_appkey'")->find();
	$app_key = $cfg["appcfg_value"];
	$cfg = $appcfg->field('appcfg_value')->where("appcfg_app=$appid AND appcfg_key='jpush_secret'")->find();
	$app_secret = $cfg["appcfg_value"];
	if( !$app_key || !$app_secret){
		return null;
	}
	return new JPush($app_key, $app_secret, RUNTIME_PATH."/jpush.log");
}

function jpush_alias($client, $jpushid, $user){
	$result = $client->device()->updateAlias($jpushid, 'u' . str_pad($user, 14, '0', STR_PAD_LEFT));
	file_put_contents(RUNTIME_PATH."/jpush.log", json_encode($result)."\n", FILE_APPEND);
}

function jpush_push($client, $userreq, $imei, $title, $extras, $device = null, $apns=true){	
	if( $device == null ){
		$device = check_device($imei);
	}
	
	$extras['imei'] = $imei;
	$extras['device_name'] = $device['device_name'];
		
	$payload = $client->push()
	->setPlatform('all')
	->addAlias('u' . str_pad($userreq, 14, '0', STR_PAD_LEFT))
	->androidNotification($title, array(
			'title' => $title,
			'extras' => $extras,
	))
	->iosNotification($title, array(
			'sound' => 'sound.caf',
			'badge' => '+1',
			'extras' => $extras,
	))
	->options(array(
			'apns_production' => $apns,
	))
	->send();
	file_put_contents(RUNTIME_PATH."/jpush.log", json_encode($payload)."\n", FILE_APPEND);

	$payload = $client->push()
	->setPlatform('all')
	->addAlias('u' . str_pad($userreq, 14, '0', STR_PAD_LEFT))
	->androidNotification($title, array(
			'title' => $title,
			'extras' => $extras,
	))
	->iosNotification($title, array(
			'sound' => 'sound.caf',
			'badge' => '+1',
			'extras' => $extras,
	))
	->options(array(
			'apns_production' => false,
	))
	->send();
	file_put_contents(RUNTIME_PATH."/jpush.log", json_encode($payload)."\n", FILE_APPEND);


	$zlt_event = M("zlt_event");
	$data1[event_type] = $extras[type];
	$data1[event_recipient] = $userreq;
	$data1[event_extra] = json_encode($extras);
	$data1[event_jpushid] = $payload['body']['msg_id'];
	$zlt_event->data($data1)->add();
	
	return $payload;
}

?>
