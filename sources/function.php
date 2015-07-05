<?php
//Various functions for common tasks

if (!defined('leafo'))
	header('Location: http://localhost/avail');

class Functions {
	var $templatedir = "templates/", $emotelist, $dir, $content;
	var $sql, $conf;
	var $template_list;
	
	//constructor
	function Functions() {
		$this->sql = &$GLOBALS['sql'];
		$this->conf = &$GLOBALS['conf'];
		$this->templatedir = $this->conf['tem_dir'];
	}
		
	function MakeCookie($name,$value) {
		$timeout = time() + 60*60*24*365; //cookie stays active for a year :o
		setcookie($name,$value,$timeout,'/','');
	}
	
	function DeleteCookie($name) {
		$timeout = time() - 60*60*24*365; //Negative time, so it dies instantly
		setcookie($name,"fgt",$timeout,'/','');
	}

	function LoadTemplate($dir) {
		//If we are reusing the last template used, dont open it again, too slow
			if ($dir != $this->dir) {
				$this->dir = $dir;
				$file = fopen($dir,"r");
				$this->content = fread($file,filesize($dir));
				fclose($file);
				return $this->content;
			} else {
				return $this->content;
		}
	}
	
	function ParseTemplate($file,$array=NULL,$u=0) {
		global $conf, $user;
		$file = $this->templatedir.$file;
		$this->template_list .= $file."<br/>";
		$content = $this->LoadTemplate($file);
		//Merge config array
		if ($array != NULL)	
			$array = array_merge($array, $conf);
		else 
			$array = $conf;
		//Merge user info
		if ($u == 1 && isset($user->info)) 
			foreach ($user->info as $key => $value)
				$array["user.".$key] = $value;
		
		if ($array != NULL)	
			foreach($array as $key => $value)
				$content = str_replace("{".$key."}",$value,$content);
				
		return $content;
	}
	
	function is_odd($n) {
		return $n & 1;
	}
	
	function convert_array($array) {  //convert aray keys to {} style.
		foreach($array as $k=>$v) { 
			$out["{".$k."}"] = $v; 
		}
		return $out;
	}
	
	function getip() {
		if (isSet($_SERVER)) {
		if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
		
		} else {
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$realip = getenv( 'HTTP_CLIENT_IP' );
		} else {
			$realip = getenv( 'REMOTE_ADDR' );
		}
		}
		return $realip;
	}
	
	function ParsePost($text,$emote) {
			
		$search = array('/\[url\]http:\/\/(.*?)\[\/url\]/i',
						'/\[url\](.*?)\[\/url\]/i',
						'/\[url\=http:\/\/(.*?)\](.*?)\[\/url\]/i',
						'/\[url\=(.*?)\](.*?)\[\/url\]/i',
						'/\[quote\](.+)\[\/quote\]/ims',
						'/\[quote\=(.*?)\](.*?)\[\/quote\]/ims',
						'/\[img\](.*?)\[\/img\]/i',
						'/\[b\](.*?)\[\/b\]/ims',
						'/\[i\](.*?)\[\/i\]/ims',
						'/\[s\](.*?)\[\/s\]/ims',
						'/\[u\](.*?)\[\/u\]/ims',
						'/\[code\](.*?)\[\/code\]/ims');
						
						
		$replace = array('<a href=\"http://$1\">http://$1</a>',
						'<a href=\"http://$1\">$1</a>',
						'<a href=\"http://$1\">$2</a>',
						'<a href=\"http://$1\">$2</a>',
						'<blockquote><b>Quote</b><br/><i>$1</i></blockquote>',
						'<blockquote>Orginally posted by <b>$1</b><br/><i>$2</i></blockquote>',
						'<img src=\"$1\" a=\"image\">',
						'<b>$1</b>',
						'<i>$1</i>',
						'<div style=\"text-decoration: line-through; display: inline;\">$1</div>',
						'<div style=\"text-decoration: underline; display: inline;\">$1</div>',
						'<blockquote><b>Code</b><br/><code>$1</code></blockquote>',);
		
		$otext = ""; while ($text != $otext) { $otext = $text; $text = preg_replace($search, $replace, $text); }
		
	
		if ($emote == 1) { //are we parsing emotes?
			//do the emotes need to be loaded
			if ($this->emotelist == null) {
				$query = $this->sql->query("SELECT text, image FROM ".$this->conf['prefix']."emoticons");
				while ($array = $this->sql->fetch_assoc($query)) {
					$this->emotelist[$array['text']] = "<img src=\"". $this->conf['img_dir_'] ."emotes/".$array['image']."\" alt=\"emotic\">";
				}
			}
			
			//convert emoticons
			foreach($this->emotelist as $k=>$v) { 
				$otext = str_replace(htmlentities($k),$v,$otext);
			}
		}
		
		return $otext;
	}
	
	function ParseLinks($text) {
		
		if(preg_match_all("/(?<!\])(?<!=)(?<!\/)(http:\/\/www.|http:\/\/|www.)(.*?)\s/i", $text." ", $regs)) {
			
			$count = count($regs[0]);
			$done = array();
			
			for ($i=0; $i<$count; $i++){
			
				//Check if it has been done already
				$skip = 0;
				for ($n=0; $n<count($done); $n++) {
					//echo $done[$n]." and ".$regs[0][$i];
					if ($done[$n] == $regs[0][$i]) 
						$skip = 1;
				}
			
				if ($skip != 1) {
					if (strpos($regs[1][$i],"www") == FALSE)
							$http = "http://";
						else
							$http = "http://www.";
				
					if (strlen($regs[0][$i]) > 40)
						$link = "[url=".$http.$regs[2][$i]."]".substr($regs[0][$i],0,25)."...".substr($regs[0][$i],-10,9)."[/url]";
					else 
						$link = "[url]".substr($regs[0][$i],0,-1)."[/url]";
					
					$text = str_replace($regs[0][$i], $link." ", $text." ");
					$done[count($done)] = $regs[0][$i];
				}
			}
			
			
		}
		return $text;
	}
	
	function Notify($class,$msg,$mod = "homeview") { //generic error ot success notice that forwards back to forum homepage by default, or module specified
		if (!isset($msg)) return 0; //prevent people calling from url
		if ($class == 0) $title = "Error";  // 0 FOR ERROR
		if ($class == 1) $title = "Success"; // 1 FOR SUCCESS
			$notice['title'] = $title;
			$notice['message'] = $msg;
			$notice['redirect'] = "?act=".$mod;
		return $this->ParseTemplate("notice.html",$notice);
	}
	
	function ArrayCombine($a, $b) {
	   $c = array();
	   $at = array_values($a);
	   $bt = array_values($b);
	   foreach($at as $key=>$aval) $c[$aval] = $bt[$key];
	   return $c;
	}
	
	function MonthArray() {
		
		$months = array(
			1 	=> 'January',
			2 	=> 'February',
			3 	=> 'March',
			4 	=> 'April',
			5 	=> 'May',
			6 	=> 'June',
			7 	=> 'July',
			8 	=> 'August',
			9 	=> 'September',
			10 	=> 'October',
			11 	=> 'November',
			12 	=> 'December'
		);
		return $months;
		
	}
	
}
?>