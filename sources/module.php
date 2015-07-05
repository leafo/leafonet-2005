<?php
/*
 * Module Class
 * Created on Apr 26, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 * 
 */
 
 class module {
 	var $sql, $func, $conf, $usr;
 	function module() {
 		//Load in referenec to global variables
 		$this->sql 		= &$GLOBALS['sql'];
		$this->conf 	= &$GLOBALS['conf'];
		$this->func 	= &$GLOBALS['func'];
		$this->user 	= &$GLOBALS['user'];
 	}
 }
?>
