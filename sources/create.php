<?php
/*
 * Title: create.php
 * Created on Apr 30, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
 class Element extends Module {
 	function Draw() {
 		return "";
 	}
 }
 
 
 /*
  *  $sql = 'CREATE TABLE `av_users` ( `id` int( 9 ) NOT NULL AUTO_INCREMENT ,'
        . ' `name` varchar( 255 ) NOT NULL default \'\','
        . ' `password` varchar( 255 ) NOT NULL default \'\','
        . ' `sex` varchar( 255 ) NOT NULL default \'male\','
        . ' `jointime` int( 9 ) NOT NULL default \'0\','
        . ' `logtime` int( 10 ) NOT NULL default \'0\','
        . ' `ip` varchar( 16 ) NOT NULL default \'\','
        . ' `title` varchar( 64 ) NOT NULL default \'\','
        . ' `email` varchar( 64 ) NOT NULL default \'\','
        . ' `template` varchar( 32 ) NOT NULL default \'\','
        . ' `browser` varchar( 64 ) NOT NULL default \'\','
        . ' `avatar` varchar( 64 ) NOT NULL default \'\','
        . ' `signarture` text NOT NULL ,'
        . ' `postcount` int( 9 ) NOT NULL default \'0\','
        . ' PRIMARY KEY ( `id` ) ) TYPE = MYISAM AUTO_INCREMENT =1';
  * 
  * 
  */
?>
