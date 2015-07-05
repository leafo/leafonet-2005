<?php
/*
 * Title: f_admin.php Forum Admin Functions
 * Created on Jun 1, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
 
class Element extends Module {
	
	var $u_dir;
	
	function Draw() {
		
		//check permissions
		if ($this->user->info['gid'] != 3)
			header('Location: http://www.leafo.net');
		elseif ($_SESSION['admin'] != 'yes')
			return $this->func->notify(0,"Must be logged in as admin before posting","admin");
		
		
		$this->u_dir = $this->conf['img_dir']."categories/";
		
		//If a function is to be called
		if (isset($_GET['f']) && is_callable(array($this, $_GET['f'])))
			return $this->$_GET['f']();
		else
			return $this->addnews();
	}
	
	//===========================================================
	//					Display Functions
	//===========================================================
	
	function AddNews() {
		
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
		
		//If image exists, upload
		if (isset($_POST['image'])) {
		
			$i_name = "news_".$_FILES['image']['name'];
				if (move_uploaded_file($_FILES['image']['tmp_name'], $this->u_dir.$i_name)) {
					//Success
				} else $this->func->notify(0,"Image can not be moved","admin");
		
		} else $i_name = "";
		
		//Submit into database
		$this->sql->query("INSERT INTO av_news SET
								cat=			'".$_POST['cat']."',
								date=			'".time()."',
								author_id=		'".$this->user->info['id']."',
								author=			'".$this->user->info['name']."',
								title=			'".addslashes(htmlentities($_POST['title']))."',
								text=			'".addslashes(htmlentities($_POST['text']))."',
								iname=			'".addslashes(htmlentities($i_name))."'");
								
		return $this->func->notify(1,"News article posted","admin");
		
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
				} else $this->func->notify(0,"Image can not be moved","admin");
				
		} else return $this->func->notify(0,"Images does not exist","admin");
		
		//Insert into database
		$this->sql->query("INSERT INTO av_n_category SET
								name=			'".$_POST['name']."',
								image=			'".$i_name."'");
		
		//return success
		return $this->func->notify(1,"Category Created","admin");
	}
	
} 

?>


