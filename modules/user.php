<?php
//Home view module..
class Element extends Module {
	function Draw() {
			//Are we calling a function?
			if (isset($_GET['f']) && is_callable(array($this, $_GET['f'])))
				return $this->$_GET['f']();
			else {
				$o['content']  = $this->func->ParseTemplate("login_view.html",NULL);
				return $this->func->ParseTemplate("account_view.html",$o);
			}
	}  
	
	function Register() {
		//Display registration form
		$o['content']  = $this->func->ParseTemplate("register_view.html",NULL);
		return $this->func->ParseTemplate("account_view.html",$o);
	}
	
	function DoLogin() {
		if(isset($_POST['pass']) && isset($_POST['user'])) {
			
			if($this->user->LogIn($_POST['user'],$_POST['pass'])) 
				return $this->func->notify(1,"You have logged in!");
			else
				return $this->func->notify(0,"Your account doesnt exist.","user");
		
		}
		
		return $this->func->notify(0,"Bad loaction, and Im sick of typing these error messages.","homeview");
	}
	
	function DoLogout() {
		if ($this->user->LogOut())
			return $this->func->notify(1,"Alright, you're logged out.","homeview");
		else
			return $this->func->notify(0,"You must be logged in to first log out. Stupid paradox.","user");
	}
	
	function DoRegister() {
		
		if(empty($_POST['pass']) || empty($_POST['user']))
			return $this->func->notify(0,"You have reached an invalid loaction!","homeview");
			
		//Check if name is taken...
		if ($this->sql->num_rows($this->sql->query("SELECT name FROM av_users WHERE name='".$_POST['user']."'")) > 0) 
			return $this->func->notify(0,"The user name is already taken! Try again.","user&f=Register");
			
		//First insert them into the USERS database
		$this->sql->query("INSERT INTO ".$this->conf['prefix']."users SET
				name 				= '".addslashes($_POST['user'])."',
				password 			= '".md5($_POST['pass'])."', 
				jointime			= '".time()."',
				ip					= '".$this->func->getip()."',
				email				= '".addslashes($_POST['email'])."',
				sex					= '".strtolower($_POST['sex'])."',
				job					= '5'"); ///PEASANT FAGGOT
		
		//Get ID number from inserting into user tables
		$lastid = mysql_insert_id();
		
		if ($lastid > 0)
		//Now create their RPG character
		$this->sql->query("INSERT INTO ".$this->conf['prefix']."rpgchar SET
				uid					= '".$lastid."'");
		
		//send message
		return $this->func->notify(1,"Thank you for registering","homeview");
				
		
	}
	 
	function Profile() {
		//is ID set?
		if (!isset($_GET['i'])) 
			return $this->func->notify(0,"Invalid member ID.","homeview");

		$query = $this->sql->query("SELECT * FROM ".$this->conf['prefix']."users AS u WHERE u.id =".$_GET['i']);
		
		if ($this->sql->n($query) == 0)
			return $this->func->notify(0,"Invalid member ID.","homeview");
		
		$array = $this->sql->f($query);
		
		$this->conf['subtitle'] = $array['name']."'s Profile";
		
		//User statistics.
		$array['days'] = floor((time() - $array['jointime']) / 86400);
		$array['joindate'] = date("F j, Y",$array['jointime']);
		if ($array['postcount'] > 0) 
		$array['average'] = round($array['postcount'] / $array['days'],2);
		else $array['average'] = 0;
		$array['signature'] = stripslashes(nl2br($this->func->ParsePost($array['signature'],1)));
		return $this->func->ParseTemplate("user_profile.html",$array);
	}
	
	/*
	 * Panel Functions
	 */ 
	
	function Panel() {
		if (!$this->user->logged) return $this->func->notify(0,"You must be a member before editing your profile.");
		$this->conf['subtitle'] = "Your Control Panel";
		return $this->PanelWrapper("<p>Welcome to your control panel. From here you can manage your settings to make your visit at leafo.net more enjoyable. Use the menu to the left to view the subsections of your control panel.</p>");
	}
	
	function PanelWrapper($contents = "",$title="Control Panel") {
		if (empty($contents))
			return  $this->func->notify(0,"Invalid function call","homeview");
		
		$ar['contents'] = $contents;
		$ar['sec_title'] = $title;
		$ar['name'] = $this->user->info['name'];
		return $this->func->ParseTemplate("user_panel_frame.html",$ar);	
	}
	
	function PanelAvatar() {
		if (!$this->user->logged) return $this->func->notify(0,"Need to be logged in to make changes to avatar");
		if (empty($this->user->info['avatar'])) {
			$array['c1'] = " checked";
			$array['c2'] = "";
			$array['avatar'] = "resource/avatar/no-avatar.gif";
			$array['av_text'] = "http://";		
		} else {
			$array['c1'] = "";
			$array['c2'] = " checked";
			$array['avatar'] = $this->user->info['avatar'];
			$array['av_text'] = $this->user->info['avatar'];
		}
		
		$this->conf['subtitle'] = "Control Panel - Edit Avatar";
		
		$array['av_x'] = $this->conf['av_x'];
		$array['av_y'] = $this->conf['av_y'];		
		return $this->PanelWrapper($this->func->ParseTemplate("user_panel_avatar.html",$array),"Edit Avatar");
	}
	
	function SubmitAvatar() {
		if (!$this->user->logged) return $this->func->notify(0,"Need to be logged in to make changes to avatar");
		if (!isset($_POST['avatar'])) return $this->func->notify(0,"Invalid Location");
		
		switch($_POST['avatar']) {
			case 0:
				//Remove Avatar
				if (!empty($this->user->info['avatar'])) {
					$this->sql->q("UPDATE av_users SET avatar='' WHERE id='".$this->user->info['id']."'");
					return $this->func->notify(1,"Avatar removed","user&f=PanelAvatar");
				} else
					return $this->func->notify(1,"No changes made","user&f=PanelAvatar");
				break;
			case 1:
				//Check remote link
				
				if (substr($_POST['av_url'],0,7) != "http://")
					$_POST['av_url'] = "http://".$_POST['av_url']; 
				
				$array = @getimagesize($_POST['av_url']);
				
				if (!$array) return $this->func->notify(0,"Invalid image file","user&f=PanelAvatar");
				if ($array[0] > $this->conf['av_x']) return $this->func->notify(0,"Selected avatar is too large","user&f=PanelAvatar");
				if ($array[1] > $this->conf['av_y']) return $this->func->notify(0,"Selected avatar is too large","user&f=PanelAvatar");
				
				//Check mime types
				if (($array['mime'] == "image/jpeg") || ($array['mime'] == "image/gif") || ($array['mime'] == "image/png")) {}
				else return $this->func->notify(0,"Selected avatar is not of correct type","user&f=PanelAvatar");
					
				//Upload settings
				$this->sql->q("UPDATE av_users SET avatar='".$_POST['av_url']."', av_x=".$array[0].", av_y=".$array[1]." WHERE id=".$this->user->info['id']);
				
				return $this->func->notify(1,"Avatar settings updated","user&f=PanelAvatar");
				
				break;
			default: 
				return $this->func-->notify(0,"Error");
		}
	}
	
	function PanelSignature() {
		if (!$this->user->logged) return $this->func->notify(0,"Need to be logged in to make changes to signature");
		
		$this->conf['subtitle'] = "Control Panel - Edit Signature";
		
		//Get signature
		$query = $this->sql->q("SELECT signature FROM av_users WHERE id=".$this->user->info['id']." LIMIT 1");
		if (mysql_num_rows($query) == 0) return $this->func->notify(0,"Need to be logged in to make changes to signature");
		
		$array = $this->sql->f($query);
		return $this->PanelWrapper($this->func->ParseTemplate("user_panel_signature.html",$array),"Edit Signature");
	}
	
	function SubmitSignature() {
		if (!$this->user->logged) return $this->func->notify(0,"Need to be logged in to make changes to signature");
		if (empty($_POST['textarea'])) return $this->func->notify(0,"Invalid Location");
		
		$sig = addslashes(htmlentities($_POST['textarea']));
		
		$this->sql->q("UPDATE av_users SET signature='".$sig."' WHERE id=".$this->user->info['id']);
		
		return $this->func->notify(1,"Signature settings updated","user&f=PanelSignature");
	}	
	
	function PanelProfile() {
		if (!$this->user->logged) return $this->func->notify(0,"Need to be logged in to have and edit your profile");
		
		$this->conf['subtitle'] = "Control Panel - Edit Profile";
		
		//Generate days shit.
		for ($i = 0; $i <= 31; $i++) {
			if ($i == $this->user->info['dob_day'])
				$selected = " selected=\"selected\"";
			else
				$selected = "";
				
			
			if ($i == 0)
				$array['days'].="<option value=\"".$i."\"".$selected."></option>\n";
			else
				$array['days'].="<option value=\"".$i."\"".$selected.">".$i."</option>\n";
		}
		
		//now for the months
		$array['months'] = "<option value=\"0\"></option>";
		foreach($this->func->montharray() as $key => $value) {
			
			if ($key == $this->user->info['dob_month'])
				$selected = " selected=\"selected\"";
			else
				$selected = "";
			
			$array['months'].= "<option value=\"".$key."\"".$selected.">".$value."</option>\n";
		}
		
		//Fix year
		if ($this->user->info['dob_year'] == 0)
			$array['year'] = 0;
		else
			$array['year'] = $this->user->info['year'];
		
		/*if ($this->user->info)*/
		
		
		return $this->PanelWrapper($this->func->ParseTemplate("user_panel_profile.html",$array,1),"Edit Profile");
		
	}	
	
	function SubmitProfile() {
		if (!$this->user->logged) return $this->func->notify(0,"Need to be logged in to have and edit your profile");
		
		$query = "UPDATE av_users SET ";
		
		if (strlen($_POST['title']) > 12) return $this->func->notify(1,"User title too long.","user&f=PanelProfile");
		
		//Clean URL
		if (substr($_POST['prof_website'],0,7) != "http://")
			$_POST['prof_website'] = "http://".$_POST['prof_website']; 
		
		$c = 0;
		foreach ($_POST as $key => $value) {
			if ($key != "submit") {
				if ($c != 0)
					$query.=", ";
				$query.=$key." = '".htmlentities($value)."'";
				$c++;
			}
		}
		
		$query.= " WHERE id=".$this->user->info['id'];
		
		$this->sql->query($query);
		
		return $this->func->notify(1,"Signature settings updated","user&f=PanelProfile");
		
	
	}
	
	
	function PMessages() {
		if (!$this->user->logged) return $this->func->notify(0,"You must be a member before using message system.");
		
		//Count the total number of pages
			$q = $this->sql->q("SELECT COUNT(*) AS count FROM av_privmsg WHERE r_id='".$this->user->info['id']."'");
			$a = $this->sql->f($q);
			
			$total_page = $a['count'] / $this->conf['pg_len'];
			if ($total_page == 1) $total_page = 1;
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
		
		
		$goob['pmax'] = $total_page;
		$goob['pnum'] = $pgnum;
		$goob['totalm'] = $a['count'];
		$goob['plow'] = $low + 1;
		
		$query = $this->sql->query("SELECT * FROM av_privmsg WHERE r_id='".$this->user->info['id']."' LIMIT ".$low.",".$this->conf['pg_len']);
		
		$goob['phigh'] = $low + $this->sql->n($query);
		
		if ($this->sql->n($query) == 0) {
			$goob['content'] = "<tr><td>No messages</td></tr>";
			return $this->PanelWrapper($this->func->parsetemplate("user_panel_message_wrapper.html",$goob,1),"Personal Message System");
		}
		
		
		while ($array = $this->sql->f($query)) {
			$goob['content'].= $this->func->parsetemplate("user_panel_message_list_1.html",$array);
		}
		
		return $this->PanelWrapper($this->func->parsetemplate("user_panel_message_wrapper.html",$goob,1),"Personal Message System");
	}
	
	function SendPM() {
		$goob['date'] = date("l \\t\h\e jS \\o\f F",time());
		if (!empty($_GET['pn']))
			$goob['prename'] = $_GET['pn'];
		else
			$goob['prename'] = "";
		return $this->PanelWrapper($this->func->parsetemplate("user_panel_sendmessage.html",$goob,1),"Personal Message System");
	}
	
	function DoSendMessage() {
		if (!$this->user->logged) return $this->func->notify(0,"You must be a member before using message system.");
		if (!isset($_POST['recipient'])) return $this->func->notify(0,"The recipient section was not filled out.","user&f=sendpm");
		if (!isset($_POST['subject'])) return $this->func->notify(0,"All messages require a subject.","user&f=sendpm");
		if (!isset($_POST['message'])) return $this->func->notify(0,"Messages may not be blank.","user&f=sendpm");
		
		//Check if use exists
		$query = $this->sql->query("SELECT id, name FROM av_users WHERE name='".$_POST['recipient']."' LIMIT 1");
		if (mysql_num_rows($query) == 0) return $this->func->notify(0,"That user/recipient does not exist in our database.","user&f=sendpm"); 
		
		$array = mysql_fetch_assoc($query);
		
		//Send
		$query = $this->sql->query("INSERT INTO av_privmsg SET
										date 	= ".time().",
										a_name 	= '".htmlentities(strip_tags($this->user->info['name']))."',
										a_id	= ".$this->user->info['id'].",
										r_name	= '".htmlentities(strip_tags($array['name']))."',
										r_id	= ".$array['id'].",
										subject	= '".htmlentities(strip_tags($_POST['subject']))."',
										text	= '".htmlentities(strip_tags($this->func->parselinks($_POST['message'])))."'");
										
		//Rockin
		return $this->func->notify(1,"Your message has bee sent to ".$array['name'].".","user&f=panel");
		
		
		
	}
}

?>