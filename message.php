<?php

// Message file
// This file should take care of anything having to do with the 
// message viewing frame.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

if($_REQUEST["action"] == "arc"){
	$thread_arc = new thread_arc($_REQUEST["id"]);
	$thread_arc->export_image();
	exit;
}
elseif($_REQUEST["action"] == "about_arc"){
	$output = $transdtd.'
		<html>
			<head>
				<title>'.WHAT_IS_THIS.'</title>
				<link rel="stylesheet" type="text/css" href="style.css" />
			</head>
			<body style="padding: 10px;">
				<h1>'.WHAT_IS_THIS.'</h1>
				<img src="images/thread-arc.gif" alt="Thread Arc" style="float: left;" />
				<p>'.WHAT_P1.'</p>
				<p>'.WHAT_P2.'</p>
				<p>'.WHAT_P3.'</p>
				<ul>
					<li>'.WHAT_L1.'</li>
					<li>'.WHAT_L2.'</li>
					<li>'.WHAT_L3.'</li>
					<li>'.WHAT_L4.'</li>
					<li>'.WHAT_L5.'</li>
					<li>'.WHAT_L6.'</li>
					<li>'.WHAT_L7.'</li>
				</ul>
				<p>'.WHAT_P4_1.' <a href="http://www.research.ibm.com/remail/threadarcs.html">'.WHAT_P4_2.'</a> '.WHAT_P4_3.' <a href="http://domino.watson.ibm.com/library/cyberdig.nsf/1e4115aea78b6e7c85256b360066f0d4/7a30ed0aac59bf5d85256d79006f272f?OpenDocument">'.WHAT_P4_4.'</a></p>
			</body>
		</html>';
	
	echo $output;
	exit;
}

if (isset($_REQUEST["id"])){
	if($_REQUEST["action"] == 'get_old_attachment'){
		get_attached_file($_REQUEST["attachment_id"],$_REQUEST["id"]);
		exit;
	}
	else{
		$message = get_message_to_view($_REQUEST["id"], $_REQUEST["action"]);
		$message_thread = new email_thread($_REQUEST["id"]);
		$thread_arc = new thread_arc($_REQUEST["id"]);
		
		if ($thread_arc->exists){
			$map = $thread_arc->get_image_map();
			$img = '<img src="'.$_SERVER["PHP_SELF"].'?action=arc&id='.$_REQUEST["id"].'" usemap="#arc_map" />';
		}
		
		$thread_nav = $message_thread->thread_nav;
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
				<b>'.SEND_TO.': </b>'.htmlentities($message["to"]).'
				<b>'.FROM.': </b>'.htmlentities($message["from"]).'<br />
				<b>'.SUBJECT.': </b>'.$message["subject"].'
			</div>
			<div id="messagebody">';

if ($map){
	$output .= '<div style="float: right; padding-left: 15px; padding-right: 15px; text-align: center;">
					<small><a href="javascript:void(0);" onclick="window.open(\''.$_SERVER["PHP_SELF"].'?action=about_arc\',\'about_arc\',config=\'height=500,width=600,toolbar=0,menubar=0,scrollbars=1,resizable=1,status=1,location=0,directories=0\');">What is this?</a></small><br />
					'.$img.'
					'.$map.'
				</div>';
}

$output .= $message["body"];

if ($thread_nav){
	$output .= $thread_nav;
}

$output .= '
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