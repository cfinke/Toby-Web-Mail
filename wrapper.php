<?php

// Wrapper file
// This file is the wrapper for the right-hand frame that contains
// the preview pane, message pane, composition frame...

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

switch($_REQUEST["action"]){
	case EMPTY_TRASH:
		empty_trash();
		break;
	case DELETE_STRING:
		delete_messages($_REQUEST["dmsg"]);
		$_REQUEST["action"] = $_REQUEST["oldaction"];
		break;
	case UNDELETE:
		undelete_messages($_REQUEST["dmsg"]);
		$action = $_REQUEST["oldaction"];
		break;
	case MOVE:
		move_messages($_REQUEST["dmsg"], $_REQUEST["movefolder"]);
		$_REQUEST["action"] = $_REQUEST["oldaction"];
		break;
	case SEND:
		send_message($_REQUEST);
		header("Location: ".$wrapperpage);
		exit;
		break;
	default:
		break;
}

$querystring = build_query_string($_REQUEST);

// If the message key has a value, store it in the session, because of it's probable length.
if (isset($_REQUEST["message"])){
	$_SESSION["toby"]["compose"]["body"] = htmlentities(stripslashes($_REQUEST["message"]));
	unset($_REQUEST["message"]);
}

$output = $framedtd . '
	<html>
		<head>
			<title>Wrapper Page</title>
		</head>';

// composerpages[] is an array of actions that should bring up the message composition page
$composerpages = array(REPLY_TO_ALL,REPLY,FORWARD,COMPOSE,TEXT_MODE,HTML_MODE);

if ($_REQUEST["action"] == OPTIONS){
	$output .= '
		<frameset rows="115,*">
			<frame src="'.$navigationpage.$querystring.'" name="navigation" frameborder="0" />
			<frame src="options.php'.$querystring.'" name="options_subpage" frameborder="0" />
		</frameset>';
}
elseif(in_array($_REQUEST["action"],$composerpages)){
	$output .= '
		<frameset rows="90,*">
			<frame src="'.$navigationpage.$querystring.'" name="navigation" frameborder="0" />
			<frame src="composer.php'.$querystring.'" name="options_subpage" frameborder="0" />
		</frameset>';
}
elseif($_REQUEST["action"] == ADDRESS_BOOK){
	$output .= '
		<frameset rows="90,*">
			<frame src="'.$navigationpage.$querystring.'" name="navigation" frameborder="0" />
			<frame src="'.$addressbookpage.$querystring.'" name="options_subpage" frameborder="0" />
		</frameset>';
}
elseif($_REQUEST["action"] == ATTACHMENTS){
	$output .= '
		<frameset rows="90,*">
			<frame src="'.$navigationpage.$querystring.'" name="navigation" frameborder="0" />
			<frame src="attachments.php'.$querystring.'" name="options_subpage" frameborder="0" />
		</frameset>';
}
else{
	// This brings up the main inbox page.
	$_SESSION["toby"]["compose"] = array();
	
	$output .= '
		<frameset rows="55%,*">
			<frame src="'.$previewpage.$querystring.'" name="preview" />
			<frame src="'.$messagepage.$querystring.'" name="message" />
		</frameset>';
}

$output .= '</html>';

echo $output;
exit;

function send_message($message){
	// This function takes care of sending an e-mail message.
	
	// If there are files attached, create a filenames[] array to pass to mail_attached
	if (is_array($_SESSION["toby"]["compose"]["attached_files"])){
		foreach($_SESSION["toby"]["compose"]["attached_files"] as $file){
			// Only add this file if it has a name.
			if (trim($file["name"] != '')){
				$filenames[] = array("file"=>$file["tmp_name"],
									 "mimetype"=>$file["mimetype"],
									 "filename"=>$file["name"]);
			}
		}
	}
	
	foreach($message as $key => $value) $message[$key] = stripslashes($value);
	
	// Mail this as a multi-part message if it has attachments or HTML content.
	$full_message = email($message["to"],
						  $message["subject"],
						  $message["message"],
						  $message["cc"],
						  $message["bcc"],
						  $filenames,
						  $message["compose_type"],
						  $message["in_reply_to"]);
	
	$full_message_parts = explode("\n\n",$full_message,2);
	$headers = $full_message_parts[0];
	$body = $full_message_parts[1];
	
	if ($_REQUEST["save_sent"]){
		save_sent($full_message, $message["save_message_in"]);
	}
	
	// Delete each of the files that were attached.
	// Because these files live in a temporary files directory, they will be deleted eventually,
	// but we delete them here just to be nice.
	if (is_array($filenames)){
		foreach($filenames as $file){
			unlink($file["file"]);
		}
	}
	
	// Clear the compose array so that none of these values are remembered.
	$_SESSION["toby"]["compose"] = array();
	
	return;
}

function email($to, $subject, $message, $cc, $bcc, $filenames, $compose_type = "text", $in_reply_to = ''){
	$unique_sep = md5(uniqid(time()));
	
	$headers = "";
	
	$optional_headers .= "To: ". $to."\n";
	
	if (strlen(trim($cc)) > 0) $headers .= "Cc: ".$cc."\n";
	if (strlen(trim($bcc)) > 0)	$headers .= "Bcc: ".$bcc."\n";
	
	$headers .= "Date: ".date("r")."\n";
	$headers .= "From: ".$_SESSION["toby"]["realname"] . "  <".$_SESSION["toby"]["email_address"].">\n";
	$headers .= "Return-Path: ".$_SESSION["toby"]["realname"] . "  <".$_SESSION["toby"]["email_address"].">\n";
	$headers .= "Message-ID: <".md5(uniqid(rand(), true))."@".$_SESSION["toby"]["texthost"].">\n";
	
	if (strlen(trim($in_reply_to)) > 0)	$headers .= "In-Reply-To: ".$in_reply_to."\n";
	
	$optional_headers .= "Subject: ".$subject."\n";
	
	if (is_array($filenames) || ($compose_type == "html")){
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: multipart/mixed; boundary=\"$unique_sep\"\n";
		$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$headers .= "If you are reading this, then your e-mail client does not support MIME.\r\n\r\n";
		$headers .= "--$unique_sep\n";
		$headers .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$headers .= html_entity_decode(strip_tags($message))."\r\n\r\n";
		
		if ($compose_type == "html"){
			$headers .= "--$unique_sep\n";
			$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
			$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$headers .= '<html>
							<body>';
			$headers .= $message;
			$headers .= '</body>
					</html>' . "\r\n\r\n";
		}
		
		if(is_array($filenames)) {
			foreach($filenames as $val) {
				if(file_exists($val['file'])) {
					$headers .= "--$unique_sep\n";
					$headers .= "Content-Type: ".$val["mimetype"]."; name=\"".$val['filename']."\"\n";
					$headers .= "Content-Transfer-Encoding: base64\n";
					$headers .= "Content-Disposition: attachment; filename=\"".$val['filename']."\"\r\n\r\n";
					$filedata = implode(file($val['file']), '');
					$headers .= chunk_split(base64_encode($filedata));
				}
			}
		}
		
		$headers .= "--$unique_sep--\n";
	}
	else{
		$headers .= "Content-Type: text/plain; charset=\"US-ASCII\"\r\n\r\n";
		$headers .= $message;
	}
	
	mail($to, $subject, '', $headers);
	
	return ($optional_headers . $headers);
}

function empty_trash(){
	$query = "DELETE FROM `email` WHERE `user`=".(0 - $_SESSION["toby"]["userid"]);
	$result = run_query($query);
	
	$query = "DELETE FROM `email_overflow` WHERE `user`=".(0 - $_SESSION["toby"]["userid"]);
	$result = run_query($query);
	
	optimize_tables();
	
	return;
}

function move_messages($messages, $folder){
	if (!is_array($messages)){
		$messages = array($_SESSION["toby"]["lastviewed"]);
	}
	
	foreach($messages as $message){
		$query = "UPDATE `email` SET `folder`=".$folder." WHERE `id`=".$message." AND `user`=".$_SESSION["toby"]["userid"]." LIMIT 1";
		$result = run_query($query);
	}
	
	return;
}

function undelete_messages($messages){
	if (!is_array($messages)){
		$messages = array($_SESSION["toby"]["lastviewed"]);
	}
	
	foreach($messages as $message){
		$query = "UPDATE `email` SET `user`=".$_SESSION["toby"]["userid"]." WHERE `id`=".$message." AND `user`=".(0 - $_SESSION["toby"]["userid"])." LIMIT 1";
		$result = run_query($query);
		
		$query = "UPDATE `email_overflow` SET `user`=".$_SESSION["toby"]["userid"]." WHERE `key`=".$message." AND `user`=".(0 - $_SESSION["toby"]["userid"]);
		$result = run_query($query);
	}
	
	return;
}

?>