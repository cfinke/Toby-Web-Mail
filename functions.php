<?php

include("message_class.php");
include("thread_class.php");

function save_message($file_array){
	// This function saves an uploaded e-mail message to the database.
	// It returns true on success and false on error.
	
	// Open the (temporary) file that was uploaded.
	$handle = fopen($file_array["tmp_name"],"r");
	
	if ($handle){
		// Read the contents into a variable.
		$contents = fread($handle, filesize($file_array["tmp_name"]));
		
		// Separate the headers and the message body.
		$parts = explode("\r\n\r\n",$contents, 2);
		$headers = trim($parts[0]);
		$body = trim($parts[1]);
		
		// Write the message to the database.
		write_message_to_database($headers, $body);
		
		// Delete the old file.
		if (unlink($file_array["tmp_name"])){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

function save_sent($message, $folder = 0){
	$message_parts = explode("\r\n\r\n",$message,2);
	$headers = trim($message_parts[0]);
	$body = trim($message_parts[1]);
	
	write_message_to_database($headers, $body, $folder, 1);
	
	return;
}

function write_message_to_database($headers, $body, $folder = 0, $save_override = 0){
	// This function writes a message to the database.
	
	if ($save_override == 1){
		$temp = 0;
	}
	else{
		$temp = (!$_SESSION["toby"]["save"] / 1);
	}
	
	$msg = new email_message($headers, $body);
	//$headers = $msg->export_headers();
	//$body = $msg->export_body();
	$length = strlen($body);
	
	$query = "INSERT INTO `email` 
				(`Return-Path`,
				 `From`,
				 `Reply-To`,
				 `To`,
				 `Subject`,
				 `Cc`,
				 `Content-Type`,
				 `Message-ID`,
				 `In-Reply-To`, 
				 `Date`,
				 `headers`,
				 `message`,
				 `user`,
				 `num_attachments`,
				 `has_html`,
				 `folder`,
				 `temp`,
				 `niceDate`) 
				 VALUES 
				 ('".str_replace("'","\'",$msg->parsed_headers["Return-Path"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["From"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["Reply-To"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["To"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["Subject"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["Cc"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["Content-Type"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["Message-ID"])."',
				  '".str_replace("'","\'",$msg->parsed_headers["In-Reply-To"])."', 
				  '".str_replace("'","\'",$msg->parsed_headers["Date"])."',
				  '".str_replace("'","\'",$headers)."',
				  '".str_replace("'","\'",substr($body,0,100000))."',
				  '".$_SESSION["toby"]["userid"]."',
				  '".$msg->num_attachments."',
				  '".(((int) $msg->has_html) / 1)."',
				  '".$folder."',
				  '".$temp."',
				  '".make_timestamp_from_date($msg->parsed_headers["Date"])."')";
	$result = run_query($query);
	
	$id = mysql_insert_id();
	
	$i = 100000;
	$key = $id;
	$part_id = 0;
	
	while ($i < $length){
		$bodypart = substr($body,$i,100000);
		
		if (strlen($bodypart) > 0){
			$query = "INSERT INTO `email_overflow` (`key`,`part_id`,`part`,`temp`,`user`) VALUES (".$key.",".$part_id++.",'".addslashes($bodypart)."','".$temp."','".$_SESSION["toby"]["userid"]."')";
			$result = run_query($query);
		}
		
		$i += 100000;
	}
	
	return;
}

function make_timestamp_from_date($date){
	// This function makes a MySQL-style timestamp (YYYYMMDDHHSSMM) from an e-mail timestamp
	$month_key = Array("jan"=>'01',"feb"=>'02',"mar"=>'03',"apr"=>'04',"may"=>'05',"jun"=>'06',"jul"=>'07',"aug"=>'08',"sep"=>'09',"oct"=>'10',"nov"=>'11',"dec"=>'12');
	
	$query = "SELECT `timezone` FROM `email_users` WHERE `id`='".$_SESSION["toby"]["userid"]."'";
	$result = run_query($query);
	$user_timezone = mysql_result($result, 0, 'timezone');
	
	// The day of the week is optional. Check for it here, and remove it.
	if (strstr($date, ",")){
		$date = explode(",",$date);
		$date = trim($date[1]);
	}
	
	// Remove double spaces.
	do {
		$tempdate = str_replace("  "," ",trim($date));
	} while (($tempdate != $date) && ($date = $tempdate));
	
	$dateparts = explode(" ",$date);
	$year = $dateparts[2];
	$month = $month_key[strtolower($dateparts[1])];
	$day = $dateparts[0];
	
	$timeparts = explode(":",$dateparts[3]);
	$hour = $timeparts[0];
	$minute = $timeparts[1];
	$second = $timeparts[2];
	
	$unix_time = mktime($hour, $minute, $second, $month, $day, $year);
	
	$timezone = substr($dateparts[4],1,4);
	$operator = (substr($dateparts[4],0,1) == "-") ? "+" : "-";	
	$adjustment = ($timezone / 100) * 3600;	
	$xxx = eval("\$unix_time $operator= \$adjustment;");
	
	$user_operator = substr($user_timezone,0,1);
	$user_timezone = substr($user_timezone,1,4);	
	$user_adjustment = ($user_timezone / 100) * 3600;
	$xxx = eval("\$unix_time $user_operator= \$user_adjustment;");
	
	$timestamp = date("YmdHis",$unix_time);
	
	return $timestamp;
}

function optimize_tables(){
	$query = "OPTIMIZE TABLE `email`";
	$result = run_query($query);
	
	$query = "OPTIMIZE TABLE `email_overflow`";
	$result = run_query($query);
	
	return;
}

################################
##### CONNECTION FUNCTIONS #####
################################

function connect_to_database(){
	global $database_host;
	global $database_user;
	global $database_password;
	global $database_name;
	
	mysql_connect($database_host,$database_user,$database_password);
	mysql_select_db($database_name);
	
	return;
}

############################
##### FOLDER FUNCTIONS #####
############################

function get_folder_dropdown($selected = null, $inbox = null){
	if ($inbox) $output = '<option value="0">'.INBOX.'</option>';
	$output .= get_dropdown_children(0, '', $selected);
	
	return $output;
}

function get_dropdown_children($id, $pre = "", $selected = null){
	$query = "SELECT * FROM `email_folders` WHERE `parent_id`=".$id." AND `user`=".$_SESSION["toby"]["userid"]." ORDER BY `folder_name`";
	$result = run_query($query);
	
	if ($id != 0){
		$newquery = "SELECT `folder_name` FROM `email_folders` WHERE `id`=".$id." AND `user`=".$_SESSION["toby"]["userid"];
		$newresult = run_query($newquery);
		$folder_name = mysql_result($newresult, 0, 'folder_name');
		$pre .= $folder_name . ' > ';
	}
	
	$list = '';
	
	if (mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_array($result)){
			$folder = $row["folder_name"];
			
			$list .= '<option value="'.$row["id"].'"';
			if ($row["id"] == $selected) $list .= ' selected="selected"';
			$list .= '>'.$pre . $folder.'</option>';
			
			$list .= get_dropdown_children($row["id"], $pre, $selected);
		}
	}
	
	return $list;
}

function delete_messages($messages, $mailbox = false){
	## This function deletes the messages specified in $messages
	## from the global mailbox or the database.
	
	// If the checked messages were in the inbox, delete here.
	if ($mailbox){
		if (is_array($messages)){
			foreach($messages as $message){
				imap_delete($mailbox, $message);
			}
			
			// Expunge the mailbox to make sure the messages stay deleted.
			imap_expunge($mailbox);
		}
	}
	
	// Delete the already archived messages here.
	else{
		if (!is_array($messages)){
			$messages = Array($_SESSION["toby"]["lastviewed"]);
		}
		
		foreach($messages as $message){
			// Deleting the message only negates the message's owner, to allow for undeletion
			// Check to make sure that the message belongs to this user as well
			$query = "UPDATE `email` SET `user`=".(0 - $_SESSION["toby"]["userid"])." WHERE `id`=".$message." AND `user`=".$_SESSION["toby"]["userid"];
			$result = run_query($query);
			
			$query = "UPDATE `email_overflow` SET `user`=".(0 - $_SESSION["toby"]["userid"])." WHERE `key`=".$message." AND `user`=".$_SESSION["toby"]["userid"];
			$result = run_query($query);
		}
	}
	
	return;
}

#############################
##### MESSAGE FUNCTIONS #####
#############################

function get_text_body($id){
	$message = create_message_object($id);
	
	return $message->export_text_body();
}

function get_html_body($id){
	$message = create_message_object($id);
	
	$body = $message->export_html_body();
	
	$body = str_replace("<a ","<a target=\"_blank\" ", $body);
	$body = str_replace("<A ","<A target=\"_blank\" ", $body);
	
	return $body;
}

function get_full_body($id){
	$message = create_message_object($id);
	
	$body = $message->export_body();
	
	return $body;
}

function get_nice_sender($from){
	## This function returns a "nice" sender name, given the value of
	## a header "From" field.  It checks to see if there is a name
	## associated with the address and return that.
	
	$sender = explode('@',$from);
	if (count($sender) > 1){
		$parts = explode(" ",$sender[0]);
		if(count($parts) > 1){
			$from = '';
			for ($i = 0; $i < count($parts) - 1; $i++){
				$from .= ' '.$parts[$i];
			}
		}
		else{
			$misc = explode(" ", $sender[1]);
			$from = $sender[0] . '@' . $misc[0];
		}
	}
	
	$from = trim(str_replace("'","",str_replace("<","",str_replace(">","",$from))));
	
	return trim($from);
}

function num_attachments($i, $mailbox = false){
	## This function returns the number of attachments that a 
	## message has.  The message is specified by the message
	## number $i (in the global mailbox).
	
	if ($mailbox){
		$num_attach = substr_count(imap_body($mailbox, $i),"filename=");
	}
	else{
		$query = "SELECT `num_attachments` FROM `email` WHERE `id` = '".$i."'";
		$result = run_query($query);
		
		$num_attach = (mysql_num_rows($result) > 0) ? mysql_result($result, 0, 'num_attachments') : 0;
	}
	
	return $num_attach;
}

function get_attached_file($k, $i){
	## This function sends the $kth file attached to the $ith message
	## straight to the browser.
	
	$body = get_full_body($i);
	$name = get_attached_file_name($k, $i, $body);
	$type = get_file_type($k, $i, $body);
	$file = get_file_contents($k, $i, $body);
	
	// Send the content type
	header("Content-type: ".$type);
	
	// Get a filename for it too
	header("Content-Disposition: attachment; filename=".$name);
	
	// Send the attachment
	echo $file;
	
	return;
}

function make_tmp_file($k, $i){
	global $temp_directory;
	
	do {
		$filename = $temp_directory.rand();
	} while(is_file($filename));
	
	$handle = fopen($filename, "w");
	
	if ($handle){
		fwrite($handle, get_file_contents($k, $i));
		fclose($handle);
		
		return $filename;
	}
	else{
		return false;
	}
}

function get_file_contents($k, $i, $body = false){
	if (!$body) $body = get_full_body($i);
	
	$parts = explode('filename="',$body);
	$part = $parts[$k];
	
	$encoding = explode("Encoding:",$parts[$k-1]);
	$encoding = explode("\n",$encoding[count($encoding)-1]);
	$encoding = trim($encoding[0]);
	
	$file = explode("\r\n\r\n",$part, 2);
	$file = explode("\n--", $file[1]);
	
	if (strtolower($encoding) == "base64") $file = base64_decode(trim($file[0]));
	else $file = trim($file[0]);
	
	return $file;
}

function get_file_type($k, $i, $body = false){
	if (!$body) $body = get_full_body($i);
	
	$parts = explode('filename="',$body);
	$part = $parts[$k];
	
	$type = strrev($parts[$k - 1]);
	$type = explode(strrev("Content-Type: "), $type, 2);
	$type = explode(";",strrev($type[0]),2);
	$type = $type[0];
	
	return $type;
}

function get_attached_file_name($k, $i, $body = false){
	## This function returns the name of the $kth attached file on the
	## $ith message in the mailbox.
	
	if (!$body) $body = get_full_body($i);
	
	$parts = explode('filename="',$body);
	
	$part = $parts[$k];
	
	$filename = explode('"',$part,2);
	$name = str_replace(" ","_",$filename[0]);
	
	return $name;
}

#############################
##### GENERAL FUNCTIONS #####
#############################

function remove_extra_newlines($text){
	## This function removes any sets of more than two newlines.
	
	do{
		$temptext = str_replace("\n\n\n", "\n\n",$text);
		$temptext = str_replace("\r", "",$temptext);
	} while (($temptext != $text) && ($text = $temptext));
	
	return $temptext;
}

function run_query($query){
	global $admin_email;
	
	$result = mysql_query($query);
	
	if (!$result){
		$trace = debug_backtrace();
		
		$error .= ERROR_DISCOVERED.":\n\n";
		$error .= $query . "\n\n";
		$error .= ERROR_IS.":\n\n";
		$error .= mysql_error() . "\n\n";
		$error .= ERROR_TRIGGER." #" . $_SESSION["toby"]["userid"] . " (".get_user_name($_SESSION["toby"]["userid"]).") ".ON." " . date("F j, Y") . " ".AT." " . date("h:i:s A T") . " ".IN_THE_FILE." " . $_SERVER["PHP_SELF"]." ".ON_LINE." " . $trace[0]["line"] .".\n\n";
		
		echo '<p>'.ERROR_MESSAGE.'</p>';
		echo '<p>'.CLICK.' <a href="'.$_SERVER["PHP_SELF"].'">'.HERE.'</a> '.TO_RETURN.'</p>';
		
		mail($admin_email, TOBY_ERROR_REPORT, $error, FROM . ": ".TOBY_ERROR_REPORT." <".$admin_email.">\n"); 
		exit;
	}
	else{
		return $result;
	}
}

function get_user_name($user_id){
	$query = "SELECT `realname` FROM `email_users` WHERE `id`=".$user_id;
	$result = run_query($query);
	
	$user_name = mysql_result($result, 0, 'realname');
	
	return $user_name;
}

function create_message_object($id){
	$query = "SELECT `headers`,`message` FROM `email` WHERE `id`=".$id;
	$result = run_query($query);
	$headers = mysql_result($result, 0, "headers");
	$body = mysql_result($result, 0, "message");
	
	$query = "SELECT * FROM `email_overflow` WHERE `key` = ".$id." ORDER BY `part_id` ASC";
	$result = run_query($query);
	
	while($row = mysql_fetch_array($result)){
		$body .= $row["part"];
	}
	
	$message = new email_message($headers, $body);
	
	return $message;
}

function get_senders($messages){
	## This function gets the senders of a specified message.
	
	$senders = '';
	
	if (!is_array($messages)){
		$messages = array((int) $_SESSION["toby"]["lastviewed"]);
	}
	
	foreach($messages as $message){
		$query = "SELECT `From` FROM `email` WHERE `id`='".$message."'";
		$result = run_query($query);
		$row = mysql_fetch_array($result);
		
		if ((mysql_num_rows($result) > 0) && ($row["From"] != "")){
			$senders .= str_replace(",","",str_replace('"',"",$row["From"])) . ', ';
		}
	}
	
	if (strlen($senders) == 2) return;
	else return $senders;
}

if (!function_exists('html_entity_decode')){
	function html_entity_decode($html){
	   $trans_table = array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES));
	   $trans_table['&#39;'] = "'";
	   
	   return strtr($html, $trans_table);
	}
}

if (!function_exists('stripos')){
	function stripos( $haystack, $needle, $start = 0){
	   $haystack = strtolower ( substr($haystack, $start) );
	   $needle = strtolower ( $needle );
	   return strpos( $haystack, $needle);
	}
}

function explodei($separator, $string, $limit = false ){
   $len = strlen($separator);
   
   for ($i = 0; ; $i++ ){
       if (($pos = stripos($string, $separator)) === false || ($limit !== false && $i > $limit - 2 )){
           $result[$i] = $string;
           break;
       }
	   
       $result[$i] = substr($string, 0, $pos);
       $string = substr($string, $pos + $len);
   }
   
   return $result;
}

function build_query_string($request_array){
	// Begin building the query string to pass values to sub-pages
	$querystring = '?action='.$request_array["action"];
	
	// exceptions[] is an array of $_REQUEST keys that should not be built into the query string
	$exceptions = array("refresh","message", "action","PHPSESSID","toby_email","toby_pass","orderby","direction");
	
	foreach ($request_array as $key => $value){
		// Add each $_REQUEST[] to the query string if it has a value
		if (!in_array($key, $exceptions) && (strlen(trim($value)) > 0)){ 
			if (is_array($value)){
				$querystring .= '&amp;'.$key.'='.urlencode(serialize($value));
			}
			else{
				$querystring .= '&amp;'.$key.'='.urlencode(stripslashes($value));
			}
		}
	}
	return $querystring;
}

function download_messages(){
	$messages = array();
	$messages_downloaded = array();
	
	if ($_SESSION["toby"]["save"] || !$_SESSION["toby"]["downloaded"]){
		$mailbox = connect_to_mailbox();
		
		// Get the total number of messages.
		$total = imap_num_msg($mailbox);
		
		// Loop backwards to have the newest message at the top.
		for($i = $total; $i > 0; $i--){
			$messages[] = $i;
		}
		
		if (is_array($messages)){
			foreach($messages as $msg){
				$totalheaders = imap_fetchheader($mailbox, $msg, FT_PREFETCHTEXT);
				$totalbody = stripslashes(imap_body($mailbox, $msg));
				
				write_message_to_database($totalheaders, $totalbody);
				
				$messages_downloaded[] = $msg;
			}
			
			$_SESSION["toby"]["downloaded"] = true;
		}
		
		if ($_SESSION["toby"]["save"]){
			delete_messages($messages_downloaded, $mailbox);
		}
		
		imap_close($mailbox);
	}
	
	return $total;
}

function connect_to_mailbox(){
	global $loginpage;
	
	$mailbox = imap_open('{'.$_SESSION["toby"]["host"].':'.$_SESSION["toby"]["port"].$_SESSION["toby"]["protocol"].'}INBOX', $_SESSION["toby"]["user"], $_SESSION["toby"]["pass"]) or die('Could not connect to '.$_SESSION["toby"]["texthost"].' ('.$_SESSION["toby"]["host"].':'.$_SESSION["toby"]["port"].') with username '.$_SESSION["toby"]["user"].': <a href="'.$loginpage.'" target="_top">Login again</a>');
	
	return $mailbox;
}

?>