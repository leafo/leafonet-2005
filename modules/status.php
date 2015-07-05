<?php
/*
 * Title: status.php
 * Created on Jun 5, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
 
class Element extends Module {
	function draw() {
		$out = "For port 6000: ";
		if ($s = @fsockopen("222.111.150.82", 6000, $e_num, $e_msg, 2)) {
			$out .= "Its up.<br>";
		} else {
			$out .= $e_num.": ".$e_msg."<br>";
		}
		
		$out .= "For port 7700: ";
		if ($s = @fsockopen("222.111.150.82", 7700, $e_num, $e_msg, 2)) {
			$out .= "Its up.";
		} else {
			$out .= $e_num.": ".$e_msg;
		}
		
		return $out;
	}
}
?>
