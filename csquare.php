<?php

function get_csquare($nlat, $nlong, $resolution) {
	if ($nlat >= 0) {
		if ($nlong >= 0) {
			$code_char1 = 1;
		} elseif ($nlong < 0) {
			$code_char1 = 7;
		}
	} elseif ($nlat < 0) {
		if ($nlong >= 0) {
			$code_char1 = 3;
		} elseif ($nlong < 0) {
			$code_char1 = 5;
		}		
	}
	
	if (abs($nlat) >= 90) {
		$code_char2 = 8;
		$lat_remainder = 9.99999;
	} else {
		$code_char2 = truncate(abs($nlat/10));
		$lat_remainder = abs($nlat) - ($code_char2 * 10);
	}
	
	if (abs($nlong) >= 180) {
		$code_chars34 = 17;
		$long_remainder = 9.99999;
	} else {
		$code_chars34 = substr('00'.truncate(abs($nlong/10)), -2);
		$long_remainder = abs($nlong) - ($code_chars34 * 10);
	}
	
	$csq_str = $code_char1.$code_char2.$code_chars34;
	
	if ($resolution == 10) {
		$cycles_required = 0;
	} elseif ($resolution == 5 || $resolution == 1) {
		$cycles_required = 1;
	} else {
		$cycles_required = 2;
	}
			
	while ($cycles_required > 0) {
		$next_triplet = get_triplet($lat_remainder, $long_remainder);
		$csq_str = $csq_str.':'.$next_triplet;
		$lat_remainder = ($lat_remainder - substr($next_triplet,1,1)) * 10;
		$long_remainder = ($long_remainder - substr($next_triplet,2,1)) * 10;
		$cycles_required = $cycles_required -1;
	}
	
	if (substr($resolution, -1) == 5) {
		$csq_str = substr($csq_str, 0, strlen($csq_str)-2);
	}
	
	return $csq_str;
	
}

function get_triplet($xLat, $xLong) {
	$digit2 = truncate(trim($xLat));
	$digit3 = truncate(trim($xLong));
	
	if (($digit2 >= 0 && $digit2 <= 4) && ($digit3 >= 0 && $digit3 <= 4)) {
		$digit1 = 1;
	} elseif (($digit2 >= 0 && $digit2 <= 4) && ($digit3 >= 5 && $digit3 <= 9)) {
		$digit1 = 2;
	} elseif (($digit2 >= 5 && $digit2 <= 9) && ($digit3 >= 0 && $digit3 <= 4)) {
		$digit1 = 3;
	} elseif (($digit2 >= 5 && $digit2 <= 9) && ($digit3 >= 5 && $digit3 <= 9)) {
		$digit1 = 4;
	}
	
	$result = $digit1.$digit2.$digit3;
	return $result;
}

function truncate($num, $digits = 0) {
	$shift = pow(10, $digits);
	return ((floor($num * $shift)) / $shift);
}

?>