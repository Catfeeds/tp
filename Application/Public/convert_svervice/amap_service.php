<?php 
define(AMAP_KEY,'8668f6085cb8575d7c340bd77040ab0b');
define(AMAP_CONVERT_URL, 'http://restapi.amap.com/v3/assistant/coordinate/convert');
function convert_coords($locs) {
	$url = AMAP_CONVERT_URL.'?key='.AMAP_KEY.'&output=json&coordsys=gps&locations='.join(";",$locs);
	try {
		$ch = curl_init();
		//参数设置
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$str = curl_exec($ch);
		curl_close($ch);
		$obj = json_decode($str);
		
		if( $obj->status != 1) {
			response(ERRNO_LOCATION_ERROR, 'convert failed');
		}
		$locstrs = split(';',$obj->locations);
		foreach( $locstrs as $loc){
			$coord = split(',', $loc);
			$result[] = array('lng'=>$coord[0], 'lat'=>$coord[1]);
		}
	}
	catch(\Exception $e) {
		//echo $e;
		response(ERRNO_LOCATION_ERROR, 'convert failed('.$e->message.')');
	}
	return $result;
}
//$locs[] = "114.01,22.60";
//$locs[] = "114.02,22.61";
//$locs[] = "114.03,22.62";
//$locs[] = "114.04,22.63";
//echo json_encode(convert_coords($locs));

?>