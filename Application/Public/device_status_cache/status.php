<?php
define(MEMCACHE_HOST, 'localhost');
define(MEMCACHE_PORT, 11211);

function device_status($imei) {
	try {
		$mem = new Memcache();
		$mem->connect(MEMCACHE_HOST, MEMCACHE_PORT);
		$status = $mem->get("$imei/status");
		return $status?1:0;
	}
	catch(\Exception $e) {
	}
	return 0;
}

function device_power($imei) {
	try {
		$mem = new Memcache();
		$mem->connect(MEMCACHE_HOST, MEMCACHE_PORT);
		$power = $mem->get("$imei/power");
		return $power;
	}
	catch(\Exception $e) {
		
	}
	return -1;
}
?>