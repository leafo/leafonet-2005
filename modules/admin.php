<?php
/*
 * Title: admin.php Forum Admin Functions
 * Created on Jun 1, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
 if (!defined('leafo'))
 	header('Location: http://www.leafo.net');
 
 
class Element extends Module {
	
	var $title, $u_dir;
	
	function Draw() {
		
		//check permissions
		if ($this->user->info['gid'] != 3)
			header('Location: http://www.leafo.net');
		elseif (isset($_POST['user']) && isset($_POST['pass']))
			return $this->AdminVerify();
		elseif ($_SESSION['admin'] != 'yes')
			return $this->func->ParseTemplate("admin_login.html");
		
		if (isset($_GET['f']) && is_callable(array($this, $_GET['f'])))
			$ar['contents'] = $this->$_GET['f']();
		else {
			$ar['contents'] = $this->func->ParseTemplate("admin_root.html", NULL);
			$this->title = "Welcome to The Core";
		}
		
		$ar['sec_title'] = $this->title;
		$this->u_dir = $this->conf['img_dir']."categories/";
		
		if (empty($_GET['n']))		
			return $this->func->ParseTemplate("admin_frame.html",$ar);
		else
			return $ar['contents'];
	}
	
	function AdminVerify() {
		
		if (!isset($_POST['user']) || !isset($_POST['pass']))
		return $this->func->notify(0,"Invalid Location","admin");
		
		$q = $this->sql->q("SELECT id FROM av_users WHERE name='".$_POST['user']."' AND password='".md5($_POST['pass'])."' AND gid=3");
		
		if ($this->sql->n($q) == 0)
			return $this->func->notify(0,"Nice try :)","homeview");
		
		$_SESSION['admin'] = "yes";
		
		return $this->func->notify(1,"Now logged in as administrator","admin");
		
	}
	
	function AdminDrop() {
		$_SESSION['admin'] = "no";
		return $this->func->notify(1,"Admin session terminated.","homeview");
	}
	



	//===========================================================
	//					Display Functions
	//===========================================================
	
	function AddNews() {
		$this->title = "Add homepage news";
		
		//Get Categories
		$sel = $this->sql->query("SELECT id, name FROM av_n_category ORDER BY name ASC");
		if (mysql_num_rows($sel) == 0) return $this->func->notify(0,"Add some categories first","admin");
		
		while ($array = $this->sql->fetch_assoc($sel)) {
			$c['cats'].= "<option value=\"".$array['id']."\">".$array['name']."</option>";
		}
		
		//return template
		return $this->func->parsetemplate("admin_add_news.html",$c);
	}
	
	function AddCat() {
		
		//return template
		return $this->func->parsetemplate("admin_add_cat.html",NULL);
	}
	
	
	
	//===========================================================
	//					Process Functions (do)
	//===========================================================
	
	function DoAddNews() {
		
		//Check if vars are set
		if (!isset($_POST['title']) || !isset($_POST['text']))
		return $this->func->notify(0,"Invalid Access","admin");
		
		
		//Image options
		switch($_POST['cat_image']) {
			case 0:
			
				$i_name = "news_".$_FILES['image']['name'];
				if (move_uploaded_file($_FILES['image']['tmp_name'], $this->u_dir.$i_name)) {
					//Success
				} else return $this->func->notify(0,"Image could not be moved","admin");
				
				break;
			case 1:
				$i_name = "";
				break;
			case 2:
				$i_name = "none";
				break;
			default:
				return $this->func->notify(0,"Shitted out","admin");
		}
		
		//Submit into database
		$this->sql->query("INSERT INTO av_news SET
								cat=			'".$_POST['cat']."',
								date=			'".time()."',
								author_id=		'".$this->user->info['id']."',
								author=			'".$this->user->info['name']."',
								title=			'".addslashes(htmlentities($_POST['title']))."',
								text=			'".addslashes(htmlentities($this->func->parselinks($_POST['text'])))."',
								iname=			'".$i_name."'");
								
		//return $this->func->notify(1,"News article posted","admin");
		
	}
	
	function DoAddCat() {
		
		//Check if vars are set
		if (!isset($_POST['name']))
		return $this->func->notify(0,"Invalid Access","admin");
		
		//Upload image
		if (isset($_FILES['image'])) {
			
				$i_name = "cat_".$_FILES['image']['name'];
				if (move_uploaded_file($_FILES['image']['tmp_name'], $this->u_dir.$i_name)) {
					//Success
				} else return $this->func->notify(0,"Image can not be moved","admin");
				
		} else return $this->func->notify(0,"Images does not exist","admin");
		
		//Insert into database
		$this->sql->query("INSERT INTO av_n_category SET
								name=			'".$_POST['name']."',
								image=			'".$i_name."'");
		
		//return success
		return $this->func->notify(1,"Category Created","admin");
	}
	
	function DoDeleteNews() {
		if (!isset($_GET['i']))
		return $this->func->notify(0,"Invalid Access","admin");
		
		$this->sql->query("DELETE FROM av_news WHERE ID = '".$_GET['i']."'");
		
		if (mysql_affected_rows() > 0)
		return $this->func->notify(1,"Article Deleted","admin"); 
		else
		return $this->func->notify(0,"Blank ID, no changes made","admin");
	}
		
	
} 

?>


