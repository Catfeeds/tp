<?php 
namespace Test\Controller;

use Test\Controller\ThinkUnitController;

class SleepController extends ThinkUnitController {	
	private $apptoken1 = '8c4e323f771e226e1a89c72ee6efedaf';
	private $apptoken2 = '8c4e323f771e226e1a89c72ee6efedcd';
	
	private function setup(){
		$userdata['user_name'] = '11111111111';
		$userdata['user_app'] = 1;
		$this->user1_id = $this->zlt_user->data($userdata)->add();
		$this->user1 = $this->zlt_user->where("user_id=$this->user1_id")->find();
		
		$usertokendata['usertoken_user'] = $this->user1_id;
		$usertokendata['usertoken_app'] = 1;
		$usertokendata['usertoken_token'] = '1111111111111111';
		$this->usertoken1_id = $this->zlt_usertoken->data($usertokendata)->add();
		
		$userdata['user_name'] = '22222222222';
		$userdata['user_app'] = 2;
		$this->user2_id = $this->zlt_user->data($userdata)->add();
		$this->user2 = $this->zlt_user->where("user_id=$this->user2_id")->find();
		
		$usertokendata['usertoken_user'] = $this->user2_id;
		$usertokendata['usertoken_app'] = 2;
		$usertokendata['usertoken_token'] = '2222222222222222';
		$this->usertoken2_id = $this->zlt_usertoken->data($usertokendata)->add();
		
		$devicedata['device_imei'] = '111111111111111';
		$devicedata['device_app'] = 1;
		$devicedata['device_master'] = $this->user1_id;
		$this->device1_id = $this->zlt_device->data($devicedata)->add();
		$this->device1 = $this->zlt_device->where("device_id=$this->device1_id")->find();
		
		$binddata['bind_device'] = $this->device1_id;
		$binddata['bind_user'] = $this->user1_id;
		$binddata['bind_valid'] = 1;
		$this->bind1_id = $this->zlt_bind->data($binddata)->add();
		
		$devicedata['device_imei'] = '222222222222222';
		$devicedata['device_app'] = 2;
		$devicedata['device_master'] = $this->user2_id;
		$this->device2_id = $this->zlt_device->data($devicedata)->add();
		$this->device2 = $this->zlt_device->where("device_id=$this->device2_id")->find();
		
		$binddata['bind_device'] = $this->device2_id;
		$binddata['bind_user'] = $this->user2_id;
		$binddata['bind_valid'] = 1;
		$this->bind2_id = $this->zlt_bind->data($binddata)->add();
		
		$devicedata['device_imei'] = '333333333333333';
		$devicedata['device_app'] = 1;
		$devicedata['device_master'] = 0;
		$this->device3_id = $this->zlt_device->data($devicedata)->add();
		$this->device3 = $this->zlt_device->where("device_id=$this->device3_id")->find();
		
		$devicedata['device_imei'] = '444444444444444';
		$devicedata['device_app'] = 1;
		$devicedata['device_master'] = $this->user2_id;
		$this->device4_id = $this->zlt_device->data($devicedata)->add();
		$this->device4 = $this->zlt_device->where("device_id=$this->device4_id")->find();
		
		$binddata['bind_device'] = $this->device4_id;
		$binddata['bind_user'] = $this->user1_id;
		$binddata['bind_valid'] = 0;
		$this->bind3_id = $this->zlt_bind->data($binddata)->add();
		
		$devicedata['device_imei'] = '555555555555555';
		$devicedata['device_app'] = 1;
		$devicedata['device_master'] = $this->user1_id;
		$this->device5_id = $this->zlt_device->data($devicedata)->add();
		$this->device5 = $this->zlt_device->where("device_id=$this->device5_id")->find();
		
		$binddata['bind_device'] = $this->device5_id;
		$binddata['bind_user'] = $this->user1_id;
		$binddata['bind_valid'] = 1;
		$this->bind4_id = $this->zlt_bind->data($binddata)->add();
		
		$binddata['bind_device'] = $this->device5_id;
		$binddata['bind_user'] = $this->user2_id;
		$binddata['bind_valid'] = 1;
		$this->bind5_id = $this->zlt_bind->data($binddata)->add();
		
		$sleepdata['devicesleep_device'] = $this->device1_id;
		$sleepdata['devicesleep_begin'] = 480;
		$sleepdata['devicesleep_end'] = 800;
		$sleepdata['devicesleep_repeat'] = '1111111';
		$this->devicesleep1_id = $this->zlt_devicesleep->data($sleepdata)->add();
	}
	
	private function teardown(){
		$this->user1_id && $this->zlt_user->where("user_id=$this->user1_id")->delete();
		$this->user2_id && $this->zlt_user->where("user_id=$this->user2_id")->delete();
		
		$this->usertoken1_id && $this->zlt_usertoken->where("usertoken_id=$this->usertoken1_id")->delete();
		$this->usertoken2_id && $this->zlt_usertoken->where("usertoken_id=$this->usertoken2_id")->delete();
		
		$this->device1_id && $this->zlt_device->where("device_id=$this->device1_id")->delete();
		$this->device2_id && $this->zlt_device->where("device_id=$this->device2_id")->delete();
		$this->device3_id && $this->zlt_device->where("device_id=$this->device3_id")->delete();
		$this->device4_id && $this->zlt_device->where("device_id=$this->device4_id")->delete();
		$this->device5_id && $this->zlt_device->where("device_id=$this->device5_id")->delete();
		
		$this->bind1_id && $this->zlt_bind->where("bind_id=$this->bind1_id")->delete();
		$this->bind2_id && $this->zlt_bind->where("bind_id=$this->bind2_id")->delete();
		$this->bind3_id && $this->zlt_bind->where("bind_id=$this->bind3_id")->delete();
		$this->bind4_id && $this->zlt_bind->where("bind_id=$this->bind4_id")->delete();
		$this->bind5_id && $this->zlt_bind->where("bind_id=$this->bind5_id")->delete();
		
		
		$this->zlt_bind->where("bind_user=$this->user1_id")->delete();
		$this->zlt_bind->where("bind_user=$this->user2_id")->delete();
		
		$this->zlt_usertoken->where("usertoken_user=$this->user1_id")->delete();
		$this->zlt_usertoken->where("usertoken_user=$this->user2_id")->delete();
		
		$this->zlt_bind->where("bind_device=$this->device1_id")->delete();
		$this->zlt_bind->where("bind_device=$this->device2_id")->delete();
		$this->zlt_bind->where("bind_device=$this->device3_id")->delete();
		$this->zlt_bind->where("bind_device=$this->device4_id")->delete();
		
		$this->zlt_devicesleep->where("devicesleep_id=$this->devicesleep1_id")->delete();
		
		$this->zlt_devicesleep->where("devicesleep_device=$this->device1_id")->delete();
	}
	
	function __construct(){
		parent::__construct();
		
		$this->zlt_user = M('zlt_user');
		$this->zlt_usertoken = M('zlt_usertoken');
		$this->zlt_device = M('zlt_device');
		$this->zlt_bind = M('zlt_bind');
		$this->zlt_devicesleep = M('zlt_devicesleep');
	}
	
	function __destruct(){
		$this->teardown();
		parent::__destruct();
	}
	
	/**
	 * @test
	 */
	function test_create(){
		$this->setup();
		
		$url = "http://app.imerit.cn:8000/test/tp/index.php?s=home/sleep/create".
			"&token=1111111111111111".
			"&imei=111111111111111".
			"&begin=23:00".
			"&end=8:00".
			"&repeat=1111111";
		$data = file_get_contents($url);
		
		$ret = json_decode($data);
		$this->assert($ret->errcode, -11);
		
		$this->teardown();
	}
	
	/**
	 * @test
	 */
	function test_index(){
		$this->setup();
		
		$url = "http://app.imerit.cn:8000/test/tp/index.php?s=home/sleep/index".
			"&token=1111111111111111".
			"&imei=111111111111111";
		$data = file_get_contents($url);
		$ret = json_decode($data);
		$this->assert($ret->errcode, 0);
		
		$this->teardown();
	}
	
	/**
	 * @test
	 */
	function test_view(){
		$this->setup();
		
		$url = "http://app.imerit.cn:8000/test/tp/index.php?s=home/sleep/view".
			"&token=1111111111111111".
			"&id=$this->devicesleep1_id";
		$data = file_get_contents($url);
		$ret = json_decode($data);
		$this->assert($ret->errcode, 0);
		
		$this->teardown();
	}
	
	/**
	 * @test
	 */
	function test_update(){
		$this->setup();
		
		$url = "http://app.imerit.cn:8000/test/tp/index.php?s=home/sleep/update".
			"&token=1111111111111111".
			"&id=$this->devicesleep1_id".
			"&begin=9:00".
			"&end=12:00";
		$data = file_get_contents($url);
		$ret = json_decode($data);
		$this->assert($ret->errcode, -11);
		
		$this->teardown();
	}
	
	/**
	 * @test
	 */
	function test_delete(){
		$this->setup();
		
		$url = "http://app.imerit.cn:8000/test/tp/index.php?s=home/sleep/delete".
			"&token=1111111111111111".
			"&id=$this->devicesleep1_id";
		$data = file_get_contents($url);
		$ret = json_decode($data);
		$this->assert($ret->errcode, -11);
		
		$this->teardown();
	}
}
?>