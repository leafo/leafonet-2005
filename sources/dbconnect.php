<?php
/*
 * SQL Database connection class
 * 
 * Author: Leafo (leaf@leafo.net)
 * Site: http://www.leafo.net
 * 
 */

class doSQL {
	var $queries;
	var $output;
	var $values = array ( "sql_database"     => ""         ,
                       		"sql_host"       => "localhost",
                       		"sql_user"       => ""         ,
                       		"sql_pass"       => ""         , );
	
	function doSQL() {
		$this->output = ""; 
		$this->queries = 0;
	}
	
	function connect() {
		$connect = mysql_connect($this->values['sql_host'], 
								$this->values['sql_user'], 
								$this->values['sql_pass']);
								
			mysql_select_db($this->values['sql_databse'],$connect) or die("Someone screwed up :D");
			$this->output.="<br><br>connected to databse: ".$this->values['sql_databse']."<br>";
	}
	
	
	function q ($query) { return $this->query($query); }
	function query($query) {
        $result = mysql_query(trim($query)) or die($query."  <br/><br/><b>Error:</b>    ".mysql_error());
        $this->queries++;
				$this->output.="query success: $query : Query number ".$this->queries." <br/>";
				return $result;
	}
	
	function bq($array) { return $this->build_query($array); }
	function build_query($array) {
		
		global $conf;
		
		if (!is_array($array))
			return 0;
		
		foreach($array as $key => $value) {
			
			if ($key == "from")
				$value = $conf['prefix'].$value;
			
			$q.= strtoupper($key)." ".$value." ";
			
		}
		
	return $this->q($q);
		
	}
	
	function fetch_array($query) {
		return mysql_fetch_array($query);
	}
	
	function f($query) { return $this->fetch_assoc($query); }
	function fetch_assoc($query) {
		return mysql_fetch_assoc($query);
	}
	
	function n($query) { return $this->num_rows($query); }
	function num_rows($query) {
		return mysql_num_rows($query);
	}

                       		
}


/* how to use:

		$sql = new doSQL;
			$sql->values['sql_user']		= 'USERNAME';
			$sql->values['sql_pass']		= 'PASSWORD';
			$sql->values['sql_databse']	= 'DATABASE';
		$sql->connect();
		
 that is all 
 
 //for debug info:
 	echo $sql->disp;
 //for amount of queries:
 	echo $sql->queries;
 
 
 */

?>
