<?php
/*
 * Title: write.php
 * Created on Apr 28, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
 class Element extends Module {
 	function Draw() {
 		//Make a sample array
 		$array['one'] = "great";
 		$array['two'] = "blate";
 		$array['three'] = "skate";
 		$array['four'] = "infiltrate";
 
 		$file = fopen("stato.ini","w");
 		fwrite($file,"[info]\n");
 		foreach($array as $key=>$value) {
 			$string = $key." = ".$value."\n";
 			fwrite($file,$string);
 		}
 		
 		$ini = parse_ini_file("stato.ini");
 		foreach($ini as $key=>$value) {
 			$s .= $key." ".$value."<br />";
 		}
 		
 		return $s;
 		
 	}
 	
 	
 		
 }
 

 
 
 
 
?>
