<?php

session_start();

include_once("config.php");

$mainpage = "client.php";
$folderpage = "folders.php";
$previewpage = "preview.php";
$wrapperpage = "wrapper.php";
$messagepage = "message.php";
$stylesheet = "style.css";
$loginpage = "index.php";
$navigationpage = "nav.php";
$mainoptionspage = "options.php";
$addressbookpage = "addressbook.php";

$langs = array("en"=>"English","es"=>"Español","de"=>"Deutsch");

$transdtd = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
$framedtd = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';

// Create the action variable.
// Check for any key/value pair where key=value, except for whitespaces.
if ((is_array($_REQUEST)) && (!isset($_REQUEST["action"]))){
	$_REQUEST["action"] = "Inbox";
	
	foreach($_REQUEST as $key => $value){
		if ($key == str_replace(" ","_",$value)){
			$_REQUEST["action"] = $value;
			break;
		}
	}
}

foreach($_REQUEST as $key => $value){
	if (($temp = @unserialize(stripslashes($value))) != false){
		if (is_array($temp)){
			$_REQUEST[$key] = $temp;
		}
	}
}

include("functions.php");

connect_to_database();

if ($_REQUEST["action"] == LOG_IN){
	$query = "SELECT * FROM `email_users` WHERE `email_address` = '".$_REQUEST["user"]."' LIMIT 1";
	$result = run_query($query);
	
	// If the user is in the users table, continue on.
	if (mysql_num_rows($result) > 0){
		$row = mysql_fetch_array($result);
		
		// Set the username and password session variables.
		$_SESSION["toby"]["user"] = $row["username"];
		$_SESSION["toby"]["pass"] = $_REQUEST["pass"];
		
		// Get the host information for this user.
		$newquery = "SELECT `name`,`port`,`protocol` FROM `email_hosts` WHERE `id` = ".$row["host"];
		$newresult = run_query($newquery);
		$newrow = mysql_fetch_array($newresult);
		
		$_SESSION["toby"]["texthost"] = $newrow["name"];
		$_SESSION["toby"]["host"] = gethostbyname($_SESSION["toby"]["texthost"]);
		$_SESSION["toby"]["port"] = $newrow["port"];
		$_SESSION["toby"]["protocol"] = $newrow["protocol"];
		$_SESSION["toby"]["userid"] = $row["id"];
		$_SESSION["toby"]["email_address"] = $row["email_address"];
		$_SESSION["toby"]["realname"] = $row["realname"];
		$_SESSION["toby"]["save"] = $row["save_messages"];
		$_SESSION["toby"]["sent_folder"] = $row["sent_folder"];
		$_SESSION["toby"]["lastviewed"] = 0;
		$_SESSION["toby"]["compose_type"] = $row["compose_type"];
		$_SESSION["toby"]["lang"] = $row["lang"];
		$_SESSION["toby"]["refresh_interval"] = $row["refresh_interval"];

		// If the remember me box was checked, set the appropriate cookies.
		if ($_REQUEST["remember"] == "yes"){
			setcookie("toby_email",$_SESSION["toby"]["email_address"],(time() + (365 * 3600 * 24)));
			setcookie("toby_pass",$_REQUEST["pass"],(time() + (365 * 3600 * 24)));
		}
		
		// Delete any old temporary messages for this user.
		delete_temp_messages();
		
		if ($_SESSION["toby"]["lang"] != $default_lang){
			header("Location: ".$mainpage);
		}
	}
	else{
		// The user was not in the users table; continue with setup.
		$_SESSION["toby"]["pass"] = $_REQUEST["pass"];
		$_SESSION["toby"]["email_address"] = strtolower($_REQUEST["user"]);
		$_REQUEST["action"] = "initial_setup";
	}
}
elseif ($_REQUEST["action"] == LOG_OUT){
	// Destroy the session variables
	unset($_SESSION["toby"]);
	
	// Expire the cookies.
	setcookie("toby_email","",time() - 1000);
	setcookie("toby_pass","",time() - 1000);
	
	// End the session
	session_destroy();
	
	$output = '
		<html>
			<head>
				<script type="text/javascript">
					<!--
					function remove_frames(){
						top.location = "'.$loginpage.'";
					}
					// -->
				</script>
			</head>
			<body onload="remove_frames();">
				'.LOG_OUT_MESSAGE.' <a href="'.$loginpage.'" target="_top">'.HERE.'</a> '.LOG_OUT_MESSAGE_END.'
			</body>
		</html>';
	
	echo $output;
	exit;
}

if (!$_SESSION["toby"]){
	header("Location: " . $loginpage);
	exit;
}

function delete_temp_messages(){
	$query = "DELETE FROM `email` WHERE (`user`=".$_SESSION["toby"]["userid"]." OR `user`=-".$_SESSION["toby"]["userid"].") AND `temp`=1";
	$result = run_query($query);
	
	$query = "DELETE FROM `email_overflow` WHERE `user`=".$_SESSION["toby"]["userid"]." AND `temp`=1";
	$result = run_query($query);
	
	optimize_tables();
}

?>
