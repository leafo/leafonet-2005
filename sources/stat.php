<?php
/*
 * Title: stat.php
 * Created on Jul 7, 2005
 *
 * Author: Leafo
 * Web: http://www.leafo.net
 */
 
 
class Stat {
	
	var $m_online, $g_online;
	var $lmember, $lid; 
	var $topic_count, $post_count, $member_count;
	var $userlist;
	
	function Stat() {
		
		global $sql;
		
		$time = 5 * 60;
		
		//Grab total number of members
		$gah = $sql->f($sql->query("SELECT count(*) AS count FROM av_users"));
		
		$this->member_count = $gah['count'];
		
		//Generate member list
		$q = $sql->q("SELECT name, id FROM av_users WHERE logtime > ".(time() - $time)."");
		$this->m_online = $sql->n($q);
		$c = 1;
		
		if (!empty($this->m_online)) {
				while($array = $sql->f($q)) {
					$this->userlist.= "<a href=\"?act=user&amp;f=profile&amp;i=".$array['id']."\">".$array['name']."</a>";
					if ($c < $this->m_online) $this->userlist.= ", ";
					$c++;
				}	
		}
		
		//Generate guest count
		$q = $sql->q("SELECT count(*) AS count FROM av_sessions WHERE time > ".(time() - $time)."");
		$array = $sql->f($q);
		$this->g_online = $array['count'];
		
		//Count Topics/Replies
		$q = $sql->q("SELECT count(*) AS count FROM av_topics");
		$array = $sql->f($q);
		$this->topic_count = $array['count'];
		
		$q = $sql->q("SELECT count(*) AS count FROM av_posts");
		$array = $sql->f($q);
		$this->post_count = $array['count'] - $this->topic_count;
		
		//Find newest member
		$q = $sql->q("SELECT name, id FROM av_users ORDER BY id DESC LIMIT 1");
		if ($sql->n($q) == 0) return;
		$array = $sql->f($q);
		$this->lmember = $array['name'];
		$this->lid = $array['id'];
		
		
		
	}
	
	function TotalBrowsing() {
		return $this->m_online + $this->g_online;
	}
	
	function InfoArray() {
		$array['gcount'] = $this->g_online;
		$array['mcount'] = $this->m_online;
		
		//Determine plural conditions
		if ($array['mcount'] == 1) {
			$array['ms'] = "";
			$array['are'] = "is";
		} else {
			$array['ms'] = "s";
			$array['are'] = "are";
		}
		
		if ($array['gcount'] == 1)
			$array['gs'] = "";
		else
			$array['gs'] = "s";
			
		$array['userlist'] = $this->userlist;
		$array['n_name'] = $this->lmember;
		$array['n_id'] = $this->lid;
		$array['topic_count'] = $this->topic_count;
		$array['post_count'] = $this->post_count;
		$array['member_count'] = $this->member_count;
		return  $array;
	}
	
}
?>
