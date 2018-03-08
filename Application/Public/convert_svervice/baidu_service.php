<?php 
define('BAIDU_KEY', 'Cjur26nklaIuNiVPlGONunyO');
define('BAIDU_CONVERT_URL', 'http://api.map.baidu.com/geoconv/v1/');
function convert_coords($locs) {
	$url = BAIDU_CONVERT_URL.'?ak='.BAIDU_KEY.'&from=1&to=5&output=json&coords='.$locs;
	$result = array();
	try{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$str = curl_exec($ch);
		curl_close($ch);
		$obj = json_decode($str);
		if( $obj->status != 0 ){
			response(ERRNO_LOCATION_ERROR, 'convert failed');
		}
		foreach( $obj->result as $loc){
			$result[] = array('lat'=>$loc->y, 'lng'=>$loc->x);
		}
	}
	catch(\Exception $e) {
		//echo $e;
		response(ERRNO_LOCATION_ERROR, 'convert failed('.$e->message.')');
	}
	return $result;
}

//
//$locs = [];
//$locs[] = "114.01,22.60";
//$locs[] = "114.02,22.61";
//$locs[] = "114.03,22.62";
//$locs[] = "114.04,22.63";
//echo json_encode(convert_coords($locs));

?>