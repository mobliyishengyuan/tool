<?php
class Lib_Position {
	// 地球半径
	const EARTH_RADIUS = 6378.137;

	/**
	 * 根据经纬度获取两点间距离
	 */
	static public function get_distance_between_two_points($lat1, $lng1, $lat2, $lng2) {
		$rad_lat1 = self::rad($lat1);
		$rad_lat2 = self::rad($lat2);
   		$a = $rad_lat1 - $rad_lat2;
   		$b = self::rad($lng1) - self::rad($lng2);

   		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($rad_lat1) * cos($rad_lat2) * pow(sin($b/2),2)));
   		$s = $s * self::EARTH_RADIUS;
   		$s = round($s * 10000) / 10000;
   		
   		return $s;
	}

	static protected function rad($d) {
		return $d * M_PI / 180.0;
	}
}
