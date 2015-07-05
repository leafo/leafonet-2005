<?php
/*

Consumable item base class

*/

 class c_base {
 	var $sql, $func, $conf, $usr;
	var $array;
 	function c_base($m_array) {
 		//Load in referenec to global variables
 		$this->sql 		= &$GLOBALS['sql'];
		$this->conf 	= &$GLOBALS['conf'];
		$this->func 	= &$GLOBALS['func'];
		$this->user 	= &$GLOBALS['user'];
		
		//Load values
		
		
		
 	}
 }

?>