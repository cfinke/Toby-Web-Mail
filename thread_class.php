<?php

class email_thread {
	var $parent_message;
	var $thread;
	var $this_id;
	var $thread_nav;
	var $num_messages = 1;
	var $node_id = 1;
	var $flat_thread = array();
	var $unseen = array();
	var $sent = array();
	
	function email_thread($id){
		if (!$id) return;
		$this->id = $id;
		
		$query = "SELECT  `id` , `From` , `To` , `Subject` , `Message-ID` , `In-Reply-To` , `Date` , `niceDate` , `seen`,`sent` ,UNIX_TIMESTAMP(`niceDate`) AS `unix_time` FROM `email` WHERE `id`='".$id."'";
		$result = run_query($query);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$this->parent_message = $this->find_parent($row["In-Reply-To"]);
		
		if (trim($this->parent_message) == ''){
			$this->parent_message = $row["Message-ID"];
			$row["selected"] = true;
		}
		else{
			$query = "SELECT  `id` , `From` , `To` , `Subject` , `Message-ID` , `In-Reply-To` , `Date` , `niceDate` , `seen`,`sent` ,UNIX_TIMESTAMP(`niceDate`) AS `unix_time` FROM `email` WHERE `Message-ID` = '".$this->parent_message."'";
			$result = run_query($query);
			
			if (mysql_num_rows($result) == 0){
				$this->parent_message = $row["Message-ID"];
				$row["selected"] = true;
			}
			else{
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
			}
		}
		
		$row["thread_id"] = "node".$this->node_id++;		
		if ($row["seen"] == 0) $this->unseen[] = $row["thread_id"];
		if ($row["sent"] == 1) $this->sent[] = $row["thread_id"];
		$this->flat_thread[$row["thread_id"]] = $row["niceDate"];
		$row["generation"] = 0;
		$row["sub_thread"] = $this->find_replies($this->parent_message);
		$this->thread[] = $row;
		
		if ($this->num_messages > 1){
			$this->thread_nav = '
				<div id="messages" style="padding-top: 10px; clear: both;">
					<table cellspacing="0" style="border: thin solid #000000;">
						<tr class="date_header">
							<td colspan="5">Thread</td>
						</tr>
						'.$this->export_thread($this->thread).'
					</table>
				</div>';
		}
		
		asort($this->flat_thread);
	}
	
	function find_parent($in_reply_to){
		$query = "SELECT `Message-ID`,`In-Reply-To` FROM `email` WHERE `Message-ID`='".$in_reply_to."' AND `Message-ID` != '' AND `user`='".$_SESSION["toby"]["userid"]."' GROUP BY `Message-ID` LIMIT 1";
		$result = run_query($query);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		if ($row["In-Reply-To"] == ''){
			return $in_reply_to;
		}
		else{
			$in_reply_to = $this->find_parent($row["In-Reply-To"]);
		}
		
		return $in_reply_to;
	}
	
	function find_replies($message_id, $generation = 0){
		$query = "SELECT  `id` , `From` , `To` , `Subject` , `Message-ID` , `In-Reply-To` , `Date` , `niceDate` , `seen`,`sent` ,UNIX_TIMESTAMP(`niceDate`) AS `unix_time` FROM `email` WHERE `In-Reply-To`='".$message_id."' AND `In-Reply-To` != '' AND `user`='".$_SESSION["toby"]["userid"]."' GROUP BY `Message-ID` ORDER BY `niceDate` ASC";
		$result = run_query($query);
		$generation++;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$this->num_messages++;
			$row["thread_id"] = "node".$this->node_id++;
			$row["generation"] = $generation;
			$this->flat_thread[$row["thread_id"]] = $row["niceDate"];
			if ($row["seen"] == 0) $this->unseen[] = $row["thread_id"];
			if ($row["sent"] == 1) $this->sent[] = $row["thread_id"];
			
			if ($row["id"] == $this->id) $row["selected"] = true;
			else $row["selected"] = false;
			$temp_messages = $this->find_replies($row["Message-ID"], $generation);
			
			if ($temp_messages != ''){
				$row["sub_thread"] = $temp_messages;
			}
			
			$messages[] = $row;
		}
		
		return $messages;
	}
	
	function export_thread($thread, $prefix = ""){
		foreach($thread as $message){
			$from_length = 25;
			$subject_length = 35;
			$to_length = 25;
			
			$nicefrom = get_nice_sender($message["From"]);
			$niceto = get_nice_sender($message["To"]);
			
			$message["attachment"] = ($message["num_attachments"] > 0) ? '<img src="images/attachsmall.gif" alt="'.ATTACHMENT.'"/>' : '&nbsp;';
			$message["subject"] = (strlen(trim($message["Subject"])) == 0) ? '['.NO_SUBJECT.']' : $message["Subject"];
			$message["viewlink"] = $messagepage.'?id='.$row["id"];
			$message["viewlink"] .= ($message["has_html"] == 1) ? '&amp;action=view_html' : '&amp;action=view';
			$message["date"] = substr($message["niceDate"],0,4).' '.substr($message["niceDate"],4,2).' '.substr($message["niceDate"],6,2);
			
			$message["from"] = '<abbr title="'.htmlentities($message["From"]).'">';
			if (strlen($nicefrom) > $from_length){
				$message["from"] .= substr($nicefrom, 0, $from_length - 2) . '...';
			}
			else{
				$message["from"] .= $nicefrom;
			}
			$message["from"] .= '</abbr>';
			
			$message["to"] = '<abbr title="'.htmlentities($message["To"]).'">';
			if (strlen($niceto) > $to_length){
				$message["to"] .= substr($niceto, 0, $to_length - 2) . '...';
			}
			else{
				$message["to"] .= $niceto;
			}
			
			$message["to"] .= '</abbr>';
			
			if (strlen($message["subject"]) > $subject_length){
				$message["subject"] = '<abbr title="'.$message["subject"].'">'.substr($message["subject"],0,$subject_length - 2).'...</abbr>';
			}
			
			$class = ($message["seen"]) ? 'seen' : 'unseen';
			$row_class = ($message["selected"]) ? 'row_selected' : 'row_unselected';
			
			$meridian = (substr($message["niceDate"],8,2) > 11) ? 'PM' : 'AM';
			$time = (((substr($message["niceDate"],8,2) - 1) % 12) + 1);
			if ($time == '0') $time = '12';
			$time .= ':' . substr($message["niceDate"],10,2) . ' ' . $meridian;
			
			$output .= '
				<tr class="'.$row_class.'">
					<td class="date"><abbr title="'.date("l F j, Y",$message["unix_time"]).' '.$time.'">'.$time.'</abbr></td>
					<td class="from">'.$message["from"].'&nbsp;</td>
					<td class="attachment">'.$message["attachment"].'</td>
					<td class="subject">';
					if ($prefix != ''){
						$output .= $prefix . '<img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" />';
					}
					
					$output .='<a href="'.$_SERVER["PHP_SELF"].'?action=view&amp;id='.$message["id"].'" class="'.$class.'">'.$message["subject"].'</a></td>
					<td class="to">'.$message["to"].'</td>
				</tr>';
			
			if (is_array($message["sub_thread"])){
				$output .= $this->export_thread($message["sub_thread"], $prefix."&nbsp;&nbsp;&nbsp;");
			}
		}
		
		return $output;
	}
}

?>