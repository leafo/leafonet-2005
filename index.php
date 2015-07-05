<?php
/*
 * This is public release version 1
 * This is a beta version still under development
 * 
 * 
 * Only edit apropriate module files located in the module directory
 * and/or edit the configvars.php located in this directory
 * 
 * If you want to, you may read the source. I left lots of comments
 * They will probably be removed for the main part on final releases
 * 
 * This release has been compiled on April 28th 2005
 * 
 * Thanks
 * 
 * (23:17:44) (+Drule) leafo hosting, you lr have to be smart if your name is not cart (that is his slogan)
 */

// return "stop it";
 
session_start();
header("Cache-control: private");

define("leafo" , 1);
define("gk394902fj" , 454536324757436326);

$title = "";

//===============================================================
// Timer Class
//===============================================================
class ParseTime {
	var $timer;
	function ParseTime() {
		$time = explode(" ", microtime());
		$this->timer = $time[0] + $time[1];
	}
	function Stop() {
		$time = explode(" ", microtime());
		$t = $time[0] + $time[1];
		return round($t - $this->timer,4);
	}
}


//Start Script Execution Timer
$timer = new ParseTime();

//Load configuration values
require "configvars.php";

//Grab the module base class
require $conf['src_dir']."module.php";

//Establish connection to sql
require $conf['src_dir']."dbconnect.php";
$sql = new doSQL();
	$sql->values['sql_user']		= $conf['sql_user'];
	$sql->values['sql_pass']		= $conf['sql_pass'];
	$sql->values['sql_databse']		= $conf['sql_database'];
	if (!empty($conf['sql_host']))
		$sql->values['sql_host']		= $conf['sql_host'];
$sql->connect();
	
//Impty and start Global Functions Class
require $conf['src_dir']."function.php";
$func = new Functions();

//Import and start User Class
require $conf['src_dir']."user.php";
$user = new User();

/*if (($user->info['name'] == 'Ketay') && ($_GET['p'] != 58))
	header("Location: http://www.leafo.net/index.php?act=forum&f=Posts&p=58&pg=1#1505");*/

//Any custom global scripts?
if (file_exists("scripts.php"))
	require "scripts.php";


//If module file exist, draw content
if (isset($_GET['act']) && is_file($conf['mod_dir'].$_GET['act'].".php"))
	require $conf['mod_dir'].$_GET['act'].".php";
else
	require $conf['mod_dir'].$conf['def_mod'].".php";
	
//Create Module Class
$mod = new Element();
	
//Generate contents from module class
$content['contents'] = $mod->Draw();
	
//Get List of all templates used for debug output
$bar['contents'] = $func->template_list;

$content['subtitle'] = $conf['subtitle'];
$content['ptime'] = "Parsed in ".$timer->Stop()." seconds.  |  ".$sql->queries." queries took place on this page. | Status: ".$user->logged;

$bar['ptime'] = "Parsed in ".$timer->Stop()." seconds.<br />".$sql->queries." queries took place on this page.";
$content['debug'] = $func->ParseTemplate("debug.html",$bar);

//Admin'd?
if ($_SESSION['admin'] == 'yes')
	$content['admin'] = "<b><i>ADMIN enabled</b></i>&nbsp;&nbsp;<a href=\"?act=admin\">Panel</a>&nbsp;&#183;&nbsp;<a href=\"?act=admin&amp;f=AdminDrop&amp;n=1\">Kill session</a>&nbsp;&#183;&nbsp;";
else
	$content['admin'] = "";

//Parse wrapper with content
echo $func->ParseTemplate("wrapper.html",$content);

/*echo '<h1>Soon</h1><div style="color: #E0E0E0; width: 50000px; height: 60000px;">there is a hidden link on this page to get past websense</div>

                                                                                                                                                                     <div style="position: absolute; left: 28353; bottom: 49283"><a href="http://leafo.net/proxy/?pass=428574" style="color: #fafafa;">proxy</a></div>';
*/
//Done!
?>
