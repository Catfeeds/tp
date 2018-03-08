<?php 
namespace Test\Controller;

use Test\Controller\ThinkUnitController;

require PUB_PATH."/lib/jpush.php";

class JPushController extends ThinkUnitController {
	/**
	 * @test
	 */
	public function test_jpush_client(){
		$this->assertTrue(jpush_client(1) != null);
		$this->assertTrue(jpush_client(2) != null);
	}
	/**
	 * @test
	 */
	public function test_jpush_push(){
		$client = jpush_client(1);
		$zlt_user = M('zlt_user');
		$user = $zlt_user->where("user_id=3885")->find();
		$zlt_device = M('zlt_device');
		$device = $zlt_device->where("device_id=114592")->find();
		$title = '绑定请求';
		$msg = "测试代码";
		$extras = array('type' => 3, 'user'=>$user['user_id'], 'user_name'=>$user['user_name'], 'msg'=>$msg, 'time' => time());
		$payload = jpush_push($client, $user['user_id'], $device['device_imei'], $title, $extras, $device);
		
		$this->assertTrue($payload['http_code'] == 200);
	}
}

?>