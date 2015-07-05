<?php

define("leafo" , 1);
define("gk394902fj" , 454536324757436326);

require "configvars.php";


require $conf['src_dir']."dbconnect.php";
$sql = new doSQL();
$sql->values['sql_user']		= $conf['sql_user'];
$sql->values['sql_pass']		= $conf['sql_pass'];
$sql->values['sql_databse']		= $conf['sql_database'];
if (!empty($conf['sql_host']))
	$sql->values['sql_host']		= $conf['sql_host'];

$sql->connect();

echo "okay";

