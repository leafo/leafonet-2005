<?php
/*
 * Title: news.php
 * Created on Jul 3, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
if (!defined('leafo'))
	header('Location: http://leafo.net');
 	
class Element extends Module {
	
	function Draw() {
		
		$c = Array();
		
		$this->conf['subtitle'] = "News - leafo hosting, you lr have to be smart if your name is not cart";
		
		if (isset($_GET['f']) && is_callable(array($this, $_GET['f'])))
			return $this->$_GET['f']();
		else {
			
			include "sources/stat.php";
			
			$stat = new Stat();
			
			$c = array_merge($c, $stat->InfoArray());
			
			$c['news'] = $this->ListNews();
			$c['poll'] = $this->ListPoll();
			$c['forum'] = $this->LatestForum();
			$c['panel'] = $this->Panel();
			return $this->func->parsetemplate("homeview.html",$c,1);
		}
		
	}
	
	//===========================================================
	//					Display Functions
	//===========================================================
	
	function ListNews() {
		
		$q = $this->sql->q("SELECT n.*, c.name, c.image FROM av_news AS n INNER JOIN av_n_category AS c ON n.cat=c.id ORDER BY n.date DESC LIMIT 5");
		
		if ($this->sql->n($q) == 0)
			return "No news posts...";
			
		while($array = $this->sql->f($q)) {
			if ($array['iname'] == "none")
				$array['display'] = "none";
			else
				$array['display'] = "block";	
				
			if  ($array['iname'] == "")
				$array['iname'] = $array['image'];
			
			$array['date'] = date("l \\t\h\e jS \\o\f F",$array['date']);
			
			foreach($array as $key => $value){
				$array[$key] = stripslashes($value);
			}				
			$array['text'] = nl2br($array['text']);
			$array['text'] = $this->func->parsepost($array['text'],1);
			
			$content.= $this->func->ParseTemplate("news_thread.html",$array);
		}
		
		return $content;
		
	}
	
	function View() {
		
		if (!isset($_GET['p'])) return $this->func->notify(0,"Id not specified");
		$q = $this->sql->q("SELECT n.*, c.name, c.image FROM av_news AS n INNER JOIN av_n_category AS c ON n.cat=c.id WHERE n.id = ".$_GET['p']." LIMIT 1");
		
		if ($this->sql->n($q) == 0)
			return $this->notify(0,"Invalid post ID");
		
		$array = $this->sql->f($q);
		
		if ($array['iname'] == "none")
				$array['display'] = "none";
			else
				$array['display'] = "block";	
		
		if  ($array['iname'] == "")
				$array['iname'] = $array['image'];
			
			$array['date'] = date("l \\t\h\e jS",$array['date']);
			
			foreach($array as $key => $value){
				$array[$key] = stripslashes($value);
			}				
			$array['text'] = nl2br($array['text']);
			$array['text'] = $this->func->parsepost($array['text'],1);
			
		$content.= $this->func->ParseTemplate("news_thread_detailed.html",$array);
		
		//Get comments
		if ($array['comments'] != 0) {
			$q = $this->sql->q("SELECT * FROM av_n_comments WHERE parent = ".$_GET['p']." ORDER BY id ASC");
			if (mysql_num_rows($q) == 0) return $this->func->notify(0,"Error, comments are not synchronized.");
			while ($array = $this->sql->f($q)) {
				if ($array['aid'] == 0) {
					$array['author'] = "A guest";
					$array['url'] = "#";
				} else {
					$array['url'] = "?act=user&f=profile&i=".$array['aid'];
				}
				$array['date'] = date("M j Y, g:i a",$array['date']);
				$content.= $this->func->ParseTemplate("news_comment.html",$array);
			}
		} else {
			$content.="&nbsp;&nbsp;&nbsp;&nbsp;There are no comments.<br/><br/>";
		}
		
		
		$gah['id'] = $_GET['p'];
		$content.= $this->func->ParseTemplate("news_post_comment.html",$gah,1);
		
		return $content;
		
		
	}
	
	
	function ListPoll() {
		
		$q = $this->sql->q("SELECT * FROM av_poll ORDER BY id DESC LIMIT 1");
		
		if ($this->sql->n($q) == 0)
			return "This will never happen";
			
		$array = $this->sql->f($q);
		
		$ip = explode("||",$array['ip']);
		
		$show_results = false;
		if (is_array($ip))
			foreach ($ip as $value)
				if ($this->func->getip() == $value)
					$show_results = true;
		
		
		if ($show_results == false) {
			$options = explode("||",$array['choices']);
			$id = 0;
			foreach($options as $value) {
				$array['opts'].= "<div><input type=\"radio\" name=\"poll\" value=\"".$id."\">".$value."</div>";
				$id++;
			}
			$array['opts'].= "<h4 class=\"center\"><input type=\"submit\" value=\"Submit\"></h4>";
		} else {
			$options = explode("||",$array['choices']);
			$answers = explode("||",$array['answers']);
			
			$total = array_sum($answers);
			
			$merge = $this->func->arraycombine($options,$answers);
			
			$array['opts'] = "<ul>";
			foreach($merge as $key => $value) {
				$percent = floor($value / $total * 100)."%";
				$array['opts'].= "<li>".$key.": <b>".$value."</b></li>";				
			}
			
			$array['opts'].= "</ul><h4>Total Votes: ".$total."</h4>";
			
		}
		
		return $this->func->parsetemplate("poll_wrapper.html",$array);
		
	}
	
	function LatestForum() {
		
		$q = $this->sql->q("SELECT title, id, lastp, lastp_date, lastp_name FROM av_topics ORDER BY lastp_date DESC LIMIT 8");
		
		if ($this->sql->n($q) == 0)
			return "No forum posts";
		
		while($ar = $this->sql->f($q)) {
			
			//Fix the title length
			if (strlen($ar['title']) > 15) {
				$ar['title'] = substr($ar['title'], 0, 15);
				$ar['title'].= "...";
			}
			
			$ar['lastp_date'] = date("n/j/y, g:i a",$ar['lastp_date']);
			
			$con.= "<div><a class=\"title-link\" href=\"?act=forum&amp;f=Posts&amp;p=".$ar['id']."&amp;pg=last#last\">".stripslashes($ar['title'])."</a> by <a href=\"?act=user&amp;f=profile&amp;i=".$ar['lastp']."\">".stripslashes($ar['lastp_name'])."</a></div><div class=\"news-icon arrow-icon margin-bottom margin-top\">on ".$ar['lastp_date']."</div>";
		}
		
		
		return $con;
	}
	
	function Panel() {
		
		//Are the logged in
		if ($this->user->logged == 1) {
			
			//Find number of PMs
			$q = $this->sql->q("SELECT COUNT(*) as count FROM av_privmsg WHERE r_id=".$this->user->info['id']." AND unread=1");
			$ar = $this->sql->f($q);
			
			$pmcount = $ar['count'];
			
			$list = "<a href=\"?act=user&amp;f=PMessages\">You have ".$pmcount." new messages</a><a href=\"?act=user&amp;f=panel\"> User panel</a><a href=\"?act=user&amp;f=dologout\">Log out</a>";
			
			if ($this->user->info['gid'] == 3) {
				
				$list.= "<a href=\"?act=admin\">Admin Panel</a>";
				
			}
			
			return $list;
		
		} 
		
		
		return "<a href=\"?act=user&amp;f=register\">Register</a><a href=\"?act=user\">Log in</a>";
		
		
	}
	
	
	//===========================================================
	//					Process Functions(do)
	//===========================================================
	 
	 
	 function SubmitPoll() {
	 	
	 	if (empty($_POST) || !is_numeric($_POST['poll']))
	 		return $this->func->notify(0,"Invalid Area Okay?");
	 	
	 	
	 	//Grab information
	 	$q = $this->sql->q("SELECT * FROM av_poll ORDER BY id DESC LIMIT 1");
	 	
	 	if ($this->sql->n($q) == 0)
			return "This will never happen";
			
		$array = $this->sql->f($q);
		
		$array['ip'].= "||".$this->func->getip();
		$answers = explode("||",$array['answers']);
	 	
	 	$answers[$_POST['poll']]++;
	 	
	 	$answers = implode("||",$answers);
	 	
	 	//reinsert info
	 	$q = $this->sql->q("UPDATE av_poll SET answers='".$answers."', ip='".$array['ip']."' WHERE id=".$array['id']."");
	 		
	 	return $this->func->notify(1,"Thank you for voting on the poll.");
	 	
	 }
	 
	 function SubmitComment() {
	 
	 	if (empty($_GET['p']) || empty($_POST['post']))
	 		return $this->func->notify(0,"Please fill out the comment form properly.");
	 		
	 	$this->sql->query("INSERT INTO av_n_comments SET 
								parent	= '".$_GET['p']."',
								aid 	= '".$this->user->info['id']."',
								author 	= '".$this->user->info['name']."',
								date	= '".time()."',
								ip		= '".$this->func->getip()."',
								message	= '".$_POST['post']."'");
								
		$this->sql->query("UPDATE av_news SET comments = comments + 1 WHERE id = ".$_GET['p']);
								
		return $this->func->notify(1,"Comment Posted","?act=homeview&f=view&p=".$_GET['p']);
	 	
	 
	 }
}
 
?>
