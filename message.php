<?php

// Message file
// This file should take care of anything having to do with the 
// message viewing frame.

include("globals.php");

if ($_REQUEST["id"] != NULL){
	if($_REQUEST["action"] == 'get_old_attachment'){
		get_attached_file($_REQUEST["attachment_id"],$_REQUEST["id"]);
		exit;
	}
	else{
		$message = get_message_to_view($_REQUEST["id"], $_REQUEST["action"]);
	}
}
else{
	$message = array();
	$message["body"] = '<div style="padding: 15px; text-align: center;">'.NO_MESSAGE.'</div>';
}

$output = '
	<html>
		<head><link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		<body>
			<div id="metamessage">
				<div id="attachment">
					'.$message["attachment"].'
				</div>
				<b>'.SEND_TO.': </b>'.$message["to"].'
				<b>'.FROM.': </b>'.$message["from"].'<br />
				<b>'.SUBJECT.': </b>'.$message["subject"].'
			</div>
			<div id="messagebody">
				'.$message["body"].'
			</div>
		</body>
	</html>';

echo $output;
exit;

function get_message_to_view($id, $action){
	## This function returns the message body and other pertinent information
	## about a specified message.
	
	global $messagepage;
	
	$query = "SELECT `seen` FROM `email` WHERE `id`=".$id;
	$result = run_query($query);
	$row = mysql_fetch_array($result);
	
	$message_object = create_message_object($id);
	$message["from"] = $message_object->parsed_headers["From"];
	$message["to"] = $message_object->parsed_headers["To"];
	$message["subject"] = $message_object->parsed_headers["Subject"];
	
	if ($action == "view_full"){
		$message["body"] = '<pre width="70">'.htmlentities($message_object->export_headers() . "\r\n\r\n" . $message_object->export_body()) . "</pre>";
	}
	elseif($action == "view_html"){
		$message["body"] = $message_object->export_html_body();
	}
	else{
		$message["body"] = $message_object->export_text_body();
		
		$message["body"] = preg_replace("/(\w+)(:\/\/)(\S+)(\s+)/U","<a href=\"\\1://\\3\" target=\"_blank\">\\1://\\3</a>\\4",$message["body"]);
		$message["body"] = ereg_replace('[_a-zA-Z0-9\-]+(\.[_a-zA-z0-9\-]+)*\@' . '[_a-zA-z0-9\-]+(\.[a-zA-z]{1,})+', '<a href="wrapper.php?action='.COMPOSE.'&amp;compose_to=\\0" target="wrapper">\\0</a>', $message["body"]);
		
		// Replace newlines with html breaks for proper display in the browser.
		$message["body"] = str_replace("\n","<br />",str_replace("=\n","<br />",str_replace("=20\n","<br />",trim($message["body"]))));
	}
	
	$message["attachment"] = '';
	
	if ($action == "view_full"){
		$message["attachment"] .= '<a href="'.$messagepage.'?action=view&amp;id='.$id.'" class="attachment_image" title="'.VIEW_REGULAR.'" target="message"><img src="images/attach.gif" alt="'.VIEW_REGULAR.'" /><span class="attachment_number">'.VIEW_REGULAR_SHORT.'</span></a>';
	}
	else{
		$message["attachment"] .= '<a href="'.$messagepage.'?action=view_full&amp;id='.$id.'" class="attachment_image" title="'.VIEW_FULL.'" target="message"><img src="images/attach.gif" alt="'.VIEW_FULL.'" /><span class="attachment_number">'.VIEW_FULL_SHORT.'</span></a>';
	}
	
	if ($message_object->has_html){
		if ($action == "view_html"){
			$message["attachment"] .= '<a href="'.$messagepage.'?action=view&amp;id='.$id.'" class="attachment_image" title="'.VIEW_TEXT.'" target="message"><img src="images/attach.gif" alt="'.VIEW_TEXT.'"/><span class="attachment_number">'.VIEW_TEXT_SHORT.'</span></a>';
		}
		else{
			$message["attachment"] .= '<a href="'.$messagepage.'?action=view_html&amp;id='.$id.'" class="attachment_image" title="'.VIEW_HTML.'" target="message"><img src="images/attach.gif" alt="'.VIEW_HTML.'"/><span class="attachment_number">'.VIEW_HTML_SHORT.'</span></a>';
		}
	}
	
	if ($message_object->num_attachments > 0){
		for ($j = 1; $j <= $message_object->num_attachments; $j++){
			$filename = get_attached_file_name($j,$id);
			
			if ($filename != 'abc'){
				$message["attachment"] .= '<a href="'.$messagepage.'?action=get_old_attachment&amp;id='.$id.'&amp;attachment_id='.$j.'" class="attachment_image" title="'.$filename.'" target="message"><img src="images/attach.gif" alt="'.ATTACHMENT.' #'.$j.': '.$filename.'"/><span class="attachment_number">'.$j.'</span></a>';
			}
		}
	}
	
	// Update the read/unread status of the message
	if (!$row["seen"]){
		$query = "UPDATE `email` SET `seen`=1 WHERE `id`=".$id;
		$result = run_query($query);
	}
	
	$_SESSION["toby"]["lastviewed"] = $id;
	
	return $message;
}

?>