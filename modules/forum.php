<?php
//Leafo.net forums
//$parsed = preg_replace("/\[img\](.*?)\[\/img\]/i", "<img src=\"$1\">", $toParse);

if (!defined('leafo'))
	header('Location: http://localhost/avail/?act=forum');

class Element extends Module {
	var $content, $notice, $prefix;

	function Draw() {
		$this->prefix = $this->conf['prefix']; //Shorten the prefix thing
		
		$this->conf['subtitle'] = "Forum";
		
		//Make a default empty location, so it doesnt show the tag
		$this->content['location'] = "";
			
		include "sources/stat.php";
		$stat = new Stat();
		$this->content['stats']  = $this->func->ParseTemplate("forum_stats.html",$stat->InfoArray());
		
		//Are we showing user panel or log in panel	
		if ($this->user->logged == true) 
			$this->content['login']  = $this->func->ParseTemplate("user_links.html",NULL,1); 
		else
			$this->content['login']  = $this->func->ParseTemplate("login_view_s.html",NULL);
			
		//If a function is to be called
		if (isset($_GET['f']) && is_callable(array($this, $_GET['f'])))
			$this->content['content'] = $this->$_GET['f']();
		else
			$this->content['content'] = $this->ListForums();
			
		//Determine if drawing stats
		if (empty($this->content['stats'])) {
				include "sources/stat.php";
			$stat = new Stat();
			$this->content['stats']  = $this->func->ParseTemplate("forum_stats.html",$stat->InfoArray());
		} elseif ($this->content['stats'] == "NULL") {
			$this->content['stats'] = "";
		}
			
		//If A notice was returned, then dont draw the rest of the forum
		if ($this->notice == 1) 
			return $this->content['content']; 
		else
			return $this->func->ParseTemplate("forum_wrapper.html",$this->content);
	}
	
	function Permissions() {
		//Parent set?
		if (!isset($_GET['p']))
			return $this->notify(0,"Invalid forum ID.");

		//Are they logged in
		if ($this->user->logged == false)
			return $this->notify(0,"You are not logged in! :(");
		
		return 1; //They pass
	}
	
	function ListForums($parent = 0) {
		$query = $this->sql->query("SELECT * FROM ".$this->prefix."forums WHERE parent='".$parent."' ORDER BY id ASC");
		if ($this->sql->num_rows($query) > 0) {
			while ($array = $this->sql->fetch_assoc($query)) {
				$qr = $this->sql->query("SELECT * FROM ".$this->prefix."forums WHERE parent='".$array['id']."'");
				if ($this->sql->num_rows($qr) > 0) {
					while ($ar = $this->sql->fetch_assoc($qr)) {
						$c = $c+1; if ($c == 3) $c = 1;
						//Generate last post information
						if ($ar['lp_date'] != 0) {
							$ar['lp_date'] = date("M j Y, g:i a",$ar['lp_date']);
							
							//Fix the title length
							if (strlen($ar['lp_title']) > 15) {
								$ar['lp_title'] = substr($ar['lp_title'], 0, 15);
								$ar['lp_title'].= "...";
							}
							
							$ar['lastpost'] = "<div><a href=\"?act=forum&f=Posts&p=".$ar['lp_id']."&amp;pg=last#last\" id=\"title_link\">".stripslashes($ar['lp_title'])."</a></div>
											   <div class=\"news-icon arrow-icon margin-top\">by <a href=\"?act=user&amp;f=profile&amp;i=".$ar['lp_aid']."\">".$ar['lp_author']."</a></div>
											   <div class=\"news-icon\">on ".$ar['lp_date']."</div>"; ///Rewrite
						} else $ar['lastpost'] = "No posts.";
						$array['child'] .= $this->func->ParseTemplate("forum_child_".$c.".html",$ar);
					}
					$return .= $this->func->ParseTemplate("forum_list.html",$array);
				}
			}
		} 
		return $return;
	} 

	function Topics() {
		
		//is the ID set..
		if (!isset($_GET['p']))  return $this->Notify(0,"Forum ID not specified.");
		
		//Generate Page information for query
		if (isset($_GET['pg'])) {
			$high = $_GET['pg']*$this->conf['pg_len'];
			$low = $high - $this->conf['pg_len'];
			$pgnum = $_GET['pg'];
		} else {
			$high = $this->conf['pg_len'];
			$low = 0;
			$pgnum = 1;
		}
		
			
		//Attempt to grab post information	
		$query = $this->sql->q("
				SELECT 
					t.id, t.title, t.description, t.replies, t.views, 
					t.authorid, t.date, t.lastp, t.lastp_date, t.lastp_name, 
					u.name AS author, f.id AS fid, f.title AS fname, t.type
				FROM 
					".$this->prefix."topics as t 
				INNER JOIN 
					".$this->prefix."users AS u ON t.authorid = u.id 
				INNER JOIN 
					".$this->prefix."forums AS f ON t.parent = f.id 
				WHERE 
					t.parent='".$_GET['p']."' 
				ORDER BY 
					t.type DESC, t.lastp_date DESC
				LIMIT 
					".$low.", ".$this->conf['pg_len']);
							 			
		//Do any posts exist here
		if ($this->sql->n($query) == 0) {
		
			$arr['child'] = "<tr><td colspan=\"5\">There are currently no threads here</td></td>";
			$index['number'] = 1;
			$arr['pages'] = $this->func->ParseTemplate("forum_page_link_current.html", $index);
			
			//Get the location
			$q = $this->sql->q("SELECT id, title FROM av_forums WHERE id=".$_GET['p']." LIMIT 1");
			
			if ($this->sql->n($q) == 0)
				return $this->notify(0,"Invalid Forum ID");
			else {
				$arg = $this->sql->f($q);
				$loctitle = $arg['title'];
				$locid = $arg['id'];
			}
			
		} else {
		
			//Count the total number of pages
			$q = $this->sql->q("SELECT COUNT(*) AS count FROM ".$this->prefix."topics WHERE parent='".$_GET['p']."'");
			$a = $this->sql->f($q);
			
			$total_page = $a['count'] / $this->conf['pg_len'];
			$total_page = ceil($total_page);
			
			//Create page links
			for ($i = 1; $i <= $total_page; $i++ ) {
				$index['number'] = $i;
				if ($i == $pgnum)
					$arr['pages'].=  $this->func->ParseTemplate("forum_page_link_current.html", $index);
				else  {
					$index['link'] = "f=Topics&p=".$_GET['p']."&pg=";
					$arr['pages'].=  $this->func->ParseTemplate("forum_page_link.html", $index);
				}
			}
				
			//Draw each topic to buffer
			while ($array = $this->sql->f($query)) {
				//Determine alterate tempalte
				$c = $c+1; if ($c == 3) $c = 1;
				
				//Format Dates
				$array['date'] = date("F jS of Y",$array['date']);
				$array['lastp_date'] = date("M d of Y, g:ia",$array['lastp_date']);
				$array['title'] = stripslashes($array['title']);
				$array['description'] = stripslashes($array['description']);
				
				//Fix the title length
				if (strlen($array['title']) > 30) {
					$array['title'] = substr($array['title'], 0, 30);
					$array['title'].= "...";
				}
				
				//Fix the desc length
				if (strlen($array['description']) > 30) {
					$array['description'] = substr($array['description'], 0, 30);
					$array['description'].= "...";
				}
				
				//Calculate pages
			
					$post_page = ($array['replies'] + 1) / $this->conf['pg_len'];
					$post_page = ceil($post_page);	
					
					//echo $post_page." pages with ".$array['replies']." replies.<br>";
				
				if ($post_page > 1) {
					if ($post_page > 3) { 
						$array['pages'] = "<b>pages</b> ( 
								<a href=\"?act=forum&amp;f=Posts&amp;p=".$array['id']."&amp;pg=1\">1</a> 
								<a href=\"?act=forum&amp;f=Posts&amp;p=".$array['id']."&amp;pg=2\">2</a> 
								<a href=\"?act=forum&amp;f=Posts&amp;p=".$array['id']."&amp;pg=3\">3</a> ... 
								<a href=\"?act=forum&amp;f=Posts&amp;p=".$array['id']."&amp;pg=last\">last</a>)";
					}
					else {
						$array['pages'] = "<b>pages</b> ( ";
						for ($i=1; $i <= $post_page; $i++) {
							$array['pages'].= "<a href=\"?act=forum&amp;f=Posts&amp;p=".$array['id']."&amp;pg=".$i."\">".$i."</a> ";
						}
						$array['pages'].= ")";				
					}
				} else
					$array['pages'] = "";	
					
				if($array['type'] == 1)
					$arr['child'] .= $this->func->ParseTemplate("forum_child_topics_sticky.html",$array);
				else
					$arr['child'] .= $this->func->ParseTemplate("forum_child_topics_".$c.".html",$array);
				
				
				//Export some location information if it hasnt been exported already
					if (!isset($loctitle))  {
						$loctitle = $array['fname'];
						$locid = $array['fid'];
					}
			}
		
		}
		
		//generate location thingy...
		if ($page > 0)
			$l['name'] = $loctitle.", Page ".($page+1);
		else 
			$l['name'] = $loctitle;
			
		$this->content['location'] = $this->func->ParseTemplate("forum_location_bland.html",$l);
		
		//grab some vars
		$arr['p'] = $_GET['p'];
		$arr['title'] = $loctitle;
		
		$out = $this->func->ParseTemplate("forum_list_topics.html",$arr);
		
		//Create the sub page list
		$ar = Array("pages" => $arr['pages']);
		$this->content['stats'] = $this->func->ParseTemplate("forum_topic_pages.html",$ar);
		
		$this->conf['subtitle'].= "- Browsing '".$loctitle."' subforum";
		
		return $out;
	}
	
	function Posts() {
		
		//check if post id is set.
			if (!isset($_GET['p'])) return $this->Notify(0,"Post ID not specified."); //error
		
		//Count the total number of pages
			$q = $this->sql->q("SELECT COUNT(*) AS count FROM ".$this->prefix."posts WHERE parent='".$_GET['p']."'");
			$a = $this->sql->f($q);
			
			$total_page = $a['count'] / $this->conf['pg_len'];
			$total_page = ceil($total_page);	
		
		
		//dertermine what page to show. first page if not set
			if (isset($_GET['pg'])) {
				if ($_GET['pg'] == 'last')
					$pgnum = $total_page;
				else
					$pgnum = $_GET['pg'];
				
				$high = $pgnum*$this->conf['pg_len'];
				$low = $high - $this->conf['pg_len'];
			} else {
				$high = $this->conf['pg_len'];
				$low = 0;
				$pgnum = 1;
			}
		
		//Get post information
		$query = $this->sql->query("
				SELECT 
					u.*, r.*, c.*, p.id AS qid, p.post, p.date, p.edit_date, p.title AS ptitle, f.id AS fid, f.title AS fname, t.title AS ttitle, t.description 
				FROM 
					".$this->prefix."posts AS p 
				INNER JOIN 
					".$this->prefix."users AS u ON p.aid = u.id 
				INNER JOIN 
					".$this->prefix."rpgjobs AS r ON u.job=r.jid 
				INNER JOIN 
					".$this->prefix."rpgchar AS c ON u.id=c.uid 
				INNER JOIN 
					".$this->prefix."topics AS t ON p.parent = t.id 
				INNER JOIN 
					".$this->prefix."forums AS f ON t.parent = f.id 
				WHERE 
					p.parent=".$_GET['p']."
				ORDER BY 
					p.id ASC 
				LIMIT 
					".$low.",".$this->conf['pg_len']);
					
			
		//Does the post exist?
			if ($this->sql->num_rows($query) == 0) return $this->Notify(0,"Empty thread ID."); //error
			
		//Add view
			$this->sql->query("UPDATE ".$this->prefix."topics SET views = views + 1 WHERE id = ".$_GET['p']);
			
			
		//Create page links
			for ($i = 1; $i <= $total_page; $i++ ) {
				$index['number'] = $i;
				if ($i == $pgnum)
					$arr['pages'].=  $this->func->ParseTemplate("forum_page_link_current.html", $index);
				else  {
					$index['link'] = "f=Posts&p=".$_GET['p']."&pg=";
					$arr['pages'].=  $this->func->ParseTemplate("forum_page_link.html", $index);
				}
			}
			
		//Draw bottom page handler
			$this->content['stats'] = $this->func->ParseTemplate("forum_topic_pages.html",$arr);
			
			while ($array = $this->sql->fetch_assoc($query)) {
				//Get some needed vars
				if (!isset($o['title'])) { 
					$o['title']  = stripslashes($array['ttitle']);
					$o['description']  = stripslashes(nl2br($array['description'])); 
					$o['author'] = stripslashes($array['name']);
					$o['id'] = $array['id'];
					$o['date'] = date("F jS of Y",$array['date']);
				}
				
				//if the avatar exists show it as image, it it doesnt show it as blank
				if ((!empty($array['avatar'])) && ($array['av_x'] != 0)) $array['avatar'] = "<img src='".$array['avatar']."' width=\"".$array['av_x']."\" height=\"".$array['av_y']."\">";
					else $array['avatar'] = "";
				
				//Format edit date
				if (($array['edit_date'] - $array['date']) > 3599)
					$array['post'].= "<br /><br /><b>Post edited on ".date("F jS of Y, g:ia",$array['edit_date'])."</b>";
					
				//Format date and posts.
				$array['date'] = date("F jS of Y, g:ia",$array['date']);
				$array['post'] = $this->func->ParsePost($array['post'],1);
				$array['post'] = stripslashes(nl2br($array['post']));
				
				//Format signature
				if (!empty($array['signature'])) {
					$tmp['signature'] = stripslashes(nl2br($this->func->ParsePost($array['signature'],1)));
					$array['signature'] = $this->func->ParseTemplate("forum_signature.html",$tmp);
				}
				
				$array['pid'] = $_GET['p'];
				
				//Show edit button if they own post, or are admin
				if ($array['id'] == $this->user->info['id'] || $this->user->info['gid'] == 3)
					$array['display'] = "span";
				else
					$array['display'] = "none";
					
				//Show delete button
				if ($this->user->info['gid'] == 3)
					$array['delete'] = "<a href=\"?act=forum&amp;f=NewQuote&amp;p={pid}&amp;q={qid}\" onClick=\"javascript:return confirm('Are you sure you want to delete this post/topic?')\"><img src=\"templates/images/button_delete.gif\" alt=\"Delete\"></a>";
				else
					$array['delete'] = "";
				
				//Draw post to buffer
				if ($array['id'] == $this->user->info['id'])
				$o['content'].= $this->func->ParseTemplate("forum_post_self.html",$array); 
				else
				$o['content'].= $this->func->ParseTemplate("forum_post_1.html",$array); 
				
				//Export some location information if it hasnt been exported already
				if (!isset($loctitle))  {
					$loctitle = $array['fname'];
					$locid = $array['fid'];
					$loctitle2 = $array['ttitle'];
				}
				
			}
			
		//Draw the post wrapper
			$o['p'] = $_GET['p'];
			$o['pages'] = $arr['pages'];
			$out  = $this->func->ParseTemplate("forum_posts_wrapper.html",$o); 
			
		//location stuffs
			$finfo['link'] = "?act=forum&f=Topics&p=".$locid;
			$finfo['name'] = $loctitle;
			$this->content['location'] = $this->func->ParseTemplate("forum_location.html",$finfo);
			if ($page > 0) //show fancy page identify if you are not on page 1
				$finfo['name'] = $loctitle2.", Page ".($page+1);
			else
				$finfo['name'] = stripslashes($loctitle2);
			$this->content['location'] .= $this->func->ParseTemplate("forum_location_bland.html",$finfo);
			
			
		return $out;
	}
	
	function NewTopic() {
		//Make sure they are logged in
		$perm = $this->Permissions();
		if ($perm != 1) return $perm;
		
		//Dont draw stats and panel
		$this->content['stats'] = "NULL";
		$this->content['login'] = "";
		
		//Get location information
		$q = $this->sql->q("SELECT title AS name FROM av_forums WHERE id = '".$_GET['p']."' LIMIT 1");
		if ($this->sql->n($q) == 0) return $this->notify(0,"Invalid forum ID.","forum");
		$array = $this->sql->f($q);
		
		$this->content['location'] = " > Posting new topic in <a href=\"?act=forumamp;f=Topics&amp;p=".$_GET['p']."\">".stripslashes($array['name'])."</a>";
		$arr['name'] = $array['name'];
		
		$this->conf['subtitle'].= " - Posting new topic in '".stripslashes($array['name'])."'";
			
		$arr['emotes'] = $this->EmoteList(true);
		$arr['parent'] = $_GET['p'];
		$out  = $this->func->ParseTemplate("forum_post_topic.html",$arr);
		return $out; 
	}
	
	function NewQuote() {
		//Find text to be quoted
		if (empty($_GET['q']))
			return $this->notify(0,"Quote error!!!","forum");
			
		$q = $this->sql->q("SELECT u.name, p.post FROM av_posts AS p INNER JOIN av_users AS u ON u.id = p.aid WHERE p.id = ".$_GET['q']." LIMIT 1");
			
		if (mysql_num_rows($q) == 0)
			return $this->notify(0,"Invalid Quote ID.","forum");
		$array = $this->sql->f($q);
		
		$text = $array['post'];
		
		$otext="";	
		while ($text != $otext) { $otext = $text; $text = preg_replace(array('/\[quote\](.+)\[\/quote\]/ims', '/\[quote\=(.+)\](.+)\[\/quote\]/ms'), array("", ""), $array['post']); }
		
		return $this->NewReply("[quote=".$array['name']."]".stripslashes($otext)."[/quote]");
	}
	
	
	function NewReply($quote="") {
		//Make sure they are logged in
		$perm = $this->Permissions();
		if ($perm != 1) return $perm;
		
		//Dont draw stats and panel
		$this->content['stats'] = "NULL";
		$this->content['login'] = "";
		
				
		$arr['emotes'] = $this->EmoteList(true);
		$arr['parent'] = $_GET['p'];
		
		//Get some information about the topic
		$q = $this->sql->query("SELECT t.title, f.title AS fname, f.id AS fid FROM av_topics AS t INNER JOIN av_forums AS f ON t.parent = f.id WHERE t.id=".$_GET['p']." LIMIT 1");
		if (mysql_num_rows($q) == 0) return $this->notify(0,"Invalid post ID.","forum");
		
		$array = $this->sql->f($q);
		$arr['title'] = $array['title'];
		
		//Build the location string
		$this->content['location'] = " > <a href=\"?act=forum&amp;f=Topics&amp;p=".$array['fid']."\">".$array['fname']."</a> > Posting a reply in <a href=\"?act=forum&amp;f=Posts&amp;p=".$_GET['p']."\">".stripslashes($arr['title'])."</a>";
		$this->conf['subtitle'].= " - Posting new reply in '".stripslashes($arr['title'])."'";
		
		//Get the last set of posts
		$q = $this->sql->query("SELECT u.name, p.post, p.date, p.aid FROM av_posts AS p INNER JOIN av_users AS u ON p.aid = u.id AND parent = '".$_GET['p']."' ORDER BY p.id DESC LIMIT 10");
		if (mysql_num_rows($q) == 0) return $this->notify(0,"Invalid post ID.","forum");
		
		$arr['number'] = mysql_num_rows($q);
		
		$c = 0;
		while($array = $this->sql->f($q)) {
			$c++;
			if ($this->func->is_odd($c))
				$array['background'] = "#F2F2F2";
			else
				$array['background'] = "#ECECEC";
			
			$array['insert'] = "[quote=".$array['name']."]".$array['post']."[/quote]";
			
			$array['date'] = date("F jS of Y, g:ia",$array['date']);
			$array['post'] = $this->func->ParsePost($array['post'],1);
			$array['post'] = stripslashes(nl2br($array['post']));
		
			
			$arr['lastposts'].= $this->func->ParseTemplate("forum_reply_replies.html",$array);
		}
		
		$arr['quote'] = $quote;
		
		$out  = $this->func->ParseTemplate("forum_post_reply.html",$arr);
		return $out; 
	}
	
	function EditPost() {
		
		$perm = $this->permissions(); 
		if ($perm != 1) 
			return $perm;
			
		$this->conf['subtitle'].= " - Editing post";
		
		$q = $this->sql->query("SELECT id, aid, parent, post FROM av_posts WHERE id=".$_GET['p']." LIMIT 1");
		if (mysql_num_rows($q) == 0)
			return $this->notify(0,"Invalid ID");
		
		$array = $this->sql->f($q);
			
		if (($array['aid'] != $this->user->info['id']) && ($this->user->info['gid'] != 3))
			return $this->notify(0,"Only the author my edit his or her post.","forum");
			
		$array['emotes'] = $this->EmoteList(true);
		$array['post'] = stripslashes($array['post']);
		
		return $this->func->ParseTemplate("forum_post_edit.html",$array);
		
	}	
	
	function DoEdit() {
		
		$perm = $this->permissions(); 
		if ($perm != 1) 
			return $perm;
			
		if (empty($_POST['textarea']))
			return $this->notify(0,"haha, no.");
		
		$q = $this->sql->query("SELECT id, aid, post FROM av_posts WHERE id=".$_GET['p']." LIMIT 1");
		if (mysql_num_rows($q) == 0)
			return $this->notify(0,"Invalid ID");
		
		$array = $this->sql->f($q);
			
		if (($array['aid'] != $this->user->info['id']) && ($this->user->info['gid'] != 3))
			return $this->notify(0,"Only the author my edit his or her post.","forum");
		
		//Update untagged links
		$post = $this->func->parselinks($_POST['textarea']);
		
		//Update
		$this->sql->query("UPDATE av_posts SET post='".htmlentities(addslashes($post))."', edit_date='".time()."' WHERE id=".$_GET['p']);
		
		return $this->Notify(1,"Post has been edited.","forum&f=Posts&p=".$_GET['t']);
		
		
	}
	
	function PostStuff() {
		
			//Parent set?
		if (!isset($_GET['p'])) { 
			return $this->notify(0,"Invalid post ID.");
		}
			//Are they logged in
		if ($this->user->logged == false) {
			$this->notify(0,"You are not logged in.");
			return $this->func->ParseTemplate("notice.html",$notice);
		}
			//Make sure its not blank
		if ($_POST['textarea'] == "")
			return $this->notify(0,"Please fill out all text areas.","forum&f=Posts&p=".$_GET['p']);
		
		//Update user statistics
		$this->UserCount(1);
		
		
		//If they are making a new topic
		if (isset($_GET['t'])) {
			
			$title	= addslashes(htmlentities($_POST['title']));
			$text 	= addslashes(htmlentities($this->func->parselinks($_POST['textarea'])));
			$desc	= addslashes(htmlentities($_POST['desc']));
			
			//Query 1 - Insert into topics table
		    $this->sql->query("INSERT INTO ".$this->prefix."topics SET 
									title		= '".$title."', 
									description	= '".$desc."', 
									authorid	= '".$this->user->info['id']."', 
									lastp 		= '".$this->user->info['id']."',
									lastp_date 	= '".time()."', 
									lastp_name 	= '".addslashes($this->user->info['name'])."', 
									date		= '".time()."', 
									status		= 1,
									parent 		= '".$_GET['p']."'");
									
			$lastid = mysql_insert_id();
			//Query 2 - Insert post information with proper parent (lastid)
			$this->sql->query("INSERT INTO ".$this->prefix."posts SET 
									parent	= '".$lastid."', 
									title	= '".$title."', 
									aid		= '".$this->user->info['id']."', 
									date	= '".time()."', 
									post 	= '".$text."', 
									ip		= '".$this->func->getip()."',
									newtopic= 1");
									
			//Query 3 - Update forum information
			$this->sql->query("UPDATE av_forums SET 
									lp_id		= '".$lastid."', 
									lp_title	= '".$title."', 
									lp_aid		= '".$this->user->info['id']."', 
									lp_author	= '".addslashes($this->user->info['name'])."', 
									lp_date 	= '".time()."', 
									topic_count = topic_count + 1 
								WHERE id=".$_GET['p']."");
			
			
			//All good - Output message
			return $this->Notify(1,"Topic Posted!","forum&f=Posts&p=".$lastid);
		}
				
			//Making a reply instead
				$text = addslashes(htmlentities($this->func->parselinks($_POST['textarea'])));
			//insert post information
				$this->sql->query("INSERT INTO ".$this->prefix."posts SET 
										parent	= '".$_GET['p']."',
										aid		= '".$this->user->info['id']."', 
										date 	= '".time()."', 
										ip		= '".$this->func->getip()."',
										post 	= '".$text."'");
				
				$pid = mysql_insert_id();
									
			//update thread information
				$this->sql->query("UPDATE ".$this->prefix."topics SET 
										lastp		= '".$this->user->info['id']."', 
										lastp_name	= '".addslashes($this->user->info['name'])."', 
										lastp_date 	= '".time()."', 
										replies 	= replies + 1 
									WHERE id = '".$_GET['p']."'");
			
			
			//grab thread information 
				$q = $this->sql->query("SELECT title, parent FROM av_topics WHERE id = '".$_GET['p']."' LIMIT 1");
				if (mysql_num_rows($q) == 0) return $this->Notify(0,"Error");
				$arr = $this->sql->f($q);
				
			//update forum information
				$this->sql->query("UPDATE av_forums SET 
										lp_id		= '".$_GET['p']."', 
										lp_title	= '".addslashes(htmlentities($arr['title']))."', 
										lp_aid		= '".$this->user->info['id']."', 
										lp_author	= '".addslashes($this->user->info['name'])."', 
										lp_date		= '".time()."', 
										post_count 	= post_count + 1 
									WHERE id=".$arr['parent']."");
				
			return $this->Notify(1,"Reply Posted!","forum&amp;f=Posts&amp;p=".$_GET['p']."&amp;pg=last#".$pid);
	}	
	
	function SyncForums() {
		$query = $this->sql->query("SELECT * FROM av_forums WHERE parent != 0");
		if (mysql_num_rows($query) == 0) return "Error";
		$status = "<br/>There are ".mysql_num_rows($query)." non base forums.<br/>
						====================================================== <br/>";
		while ($ar = $this->sql->f($query)) {
			
			$status.="Synchronizing '".$ar['title']."' <br/>";
			
			$status.="Getting Last post... <br/>";
			$q = $this->sql->query("SELECT t.id, t.title, p.date, p.aid, u.name as author FROM av_topics AS t INNER JOIN av_posts AS p ON t.id = p.parent INNER JOIN av_users AS u ON p.aid = u.id WHERE t.parent = ".$ar['id']." ORDER BY p.id DESC LIMIT 1");
			if (mysql_num_rows($q) == 0) {
				$time = 0;
				$post_count = 0;
				$topic_count = 0;
				$status.="<div style=\"padding-left: 20px;\">No last post, aborting.</div>";
			} else {
				
				$array = mysql_fetch_assoc($q);
				$id 	= $array['id'];
				$title	= $array['title'];
				$aid 	= $array['aid'];
				$author	= $array['author'];
				$time	= $array['date'];
				
				$status.="Inserting last post information: <div style=\"padding-left: 20px;\">
							id = ".$array['id']." <br/>
							title = ".$array['title']." <br/>
							aid = ".$array['aid']." <br/>
							author = ".$array['author']." <br/>
							time = ".$array['date']." <br/></div>";
				
				$topic_count = mysql_num_rows($this->sql->query("SELECT id FROM av_topics WHERE parent = ".$ar['id']));
				
				$raw_post_count = mysql_num_rows($this->sql->query("SELECT p.id FROM av_posts AS p INNER JOIN av_topics AS t ON p.parent = t.id WHERE t.parent = '".$ar['id']."'"));
				$post_count = $raw_post_count - $topic_count;
				
				
				$status.="Inserting count totals <div style=\"padding-left: 20px;\">
							topic_count = ".$topic_count." <br/>
							raw_post_count = ".$raw_post_count." <br/>
							post_count = ".$post_count."</div>";
				
			}
			
			$this->sql->query("UPDATE av_forums SET 
						lp_id='".$id."', 
						lp_title='".$title."', 
						lp_aid='".$aid."', 
						lp_author='".$author."', 
						lp_date='".$time."', 
						post_count = '".$post_count."',
						topic_count = '".$topic_count."'
					WHERE id=".$ar['id']."");
			
			$status.="Synchronization complete <br/>";
					
			$status.="====================================================== <br/>";
		}
		
		return $status."<br/>";
	}
	
	function EmoteList($clickable) {
		if ($clickable == true)  $where = " WHERE clickable=1";  else $where = "";
		$query = $this->sql->query("SELECT text, image FROM ".$this->prefix."emoticons".$where);
		if ($this->sql->num_rows($query) > 0) {
			while ($array = $this->sql->fetch_assoc($query)) {
				$out.= $this->func->ParseTemplate("forum_emotes.html",$array);
			}
			return $out;
		} else return NULL;
	}
	
	function UserCount($num = 0, $ntopic = 0) { 
		//increast post count, etc.
		$moneys = $this->user->info['money'] + rand(rand(1,2),rand(4,5));
		$pcount = $this->user->info['postcount']+1;
		$this->sql->query("UPDATE ".$this->prefix."users AS u SET u.postcount='".$pcount."', u.money='".$moneys."' WHERE u.id='".$this->user->info['id']."'");
	}
	
	function Notify($class,$msg,$mod = "forum") {
		if (!isset($msg)) return 0; //prevent people calling from url
		if ($class == 0) $title = "Error";  // 0 FOR ERROR
		if ($class == 1) $title = "Success"; // 1 FOR SUCCESS
			$notice['title'] = $title; $this->notice = 1;
			$notice['message'] = $msg;
			$notice['redirect'] = "?act=".$mod;
		return $this->func->ParseTemplate("notice.html",$notice);
	}
	
}
