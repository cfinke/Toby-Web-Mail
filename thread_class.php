<?php

class email_thread{
	var $parent_message;
	var $thread;
	
	function email_thread($id){
		$query = "SELECT `Message-ID`,`In-Reply-To`,`id` FROM `email` WHERE `id`='".$id."'";
		$result = run_query($query);
		$row = mysql_fetch_Array($result);
		$this->parent_message = $this->find_parent($row["In-Reply-To"]);
	}
	
	function find_parent($in_reply_to){
		$query = "SELECT `In-Reply-To` FROM `email` WHERE `Message-ID`='".$in_reply_to."' AND `Message-ID` != '' AND `user`='".$_SESSION["toby"]["userid"]."' LIMIT 1";
		$result = run_query($query);
		
		if (mysql_num_rows($result) > 0){
			$row = mysql_fetch_array($result);
			$in_reply_to = $this->find_parent($row["In-Reply-To"]);
		}
		else{
			return $in_reply_to;
		}
	}
	
	function find_replies($message_id){
		$query = "SELECT `Message-ID`,`In-Reply-To`,`id` FROM `email` WHERE `In-Reply-To`='".$message_id."' AND `In-Reply-To` != '' AND `user`='".$_SESSION["toby"]["userid"]."' ";
		$result = run_query($query);
		
		while ($row = mysql_Fetch_array($result)){
			$messages[] = $row;
			$messages[] = $this->find_replies($row["Message-ID"]);
		}
		
		return $messages;
	}
}

?>