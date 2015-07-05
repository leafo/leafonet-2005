<?php
//user class... to check log in, etc.
class User {
	var $info, $logged, $leafo;
	var $sql, $conf, $func;
	
	function User() {
		$this->sql 	= &$GLOBALS['sql'];
		$this->conf = &$GLOBALS['conf'];
		$this->func = &$GLOBALS['func'];
		$this->logged = 0;
		//cookies set? Check if they are logged in\
		if (isset($_COOKIE[$this->conf['prefix'].'uid']) && isset($_COOKIE[$this->conf['prefix'].'pass'])) {
			$id = $_COOKIE[$this->conf['prefix'].'uid'];
			$pass = $_COOKIE[$this->conf['prefix'].'pass'];
			$query = $this->sql->query("SELECT * FROM ".$this->conf['prefix']."users  WHERE name='".$id."' AND password='".$pass."' LIMIT 1");
			if ($this->sql->num_rows($query) > 0) {
				$this->logged = 1;
				$this->info = $this->sql->fetch_assoc($query);
				$this->DoSession($this->info['id']);
				//Are they admin?
				if ($this->info['ugroup'] == 1)
					$this->logged = 2;
				
			}
		} else  {
			
			$this->logged = 0;
			//Create Guest session
			$this->sql->q("REPLACE av_sessions SET ip='".$this->func->getip()."', time=".time()."");
			$this->info['name'] = "Guest";
		
		}
	}
	
	function LogIn($username,$password) {
		$query = $this->sql->query("SELECT * FROM ".$this->conf['prefix']."users WHERE name='".$username."' AND password='".md5($password)."' LIMIT 1");
		if ($this->sql->num_rows($query) == 0) return false;
			$this->info = $this->sql->fetch_assoc($query);
			//Set cookies
				$this->func->MakeCookie($this->conf['prefix']."uid",$this->info['name']);
				$this->func->MakeCookie($this->conf['prefix']."pass",$this->info['password']);
			//Do other stuff
				$this->logged = true;
				$this->DoSession($this->info['id']);
				return true;
	}
	
	function LogOut() {
		if (!$this->logged) return false; 
		//Trash cookies
			$this->func->DeleteCookie($this->conf['prefix']."uid");
			$this->func->DeleteCookie($this->conf['prefix']."pass");
			$this->logged = false;
			$this->DoSession($this->info['id']);
			return true;
	}
	
	function DoSession($id) {
		//making/refreshing or destroying session...
		if ($this->logged == true) {
			$set = time();
			$_SESSION['logged'] = true;	
		}
		else $set = 0;
		$this->sql->query("UPDATE ".$this->conf['prefix']."users SET logtime=".$set." WHERE id=".$id."");
	}
	
	
}


?>