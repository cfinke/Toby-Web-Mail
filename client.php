<?php

// Client wrapper page.
// This page contains the setup for the main layout of the program.
// It is the page that is called from the login screen.

error_reporting(0);

include_once("config.php");

// If the "Remember me" box was checked, set the action and the username and password
if ($_REQUEST["_r"] == "t"){
	$_REQUEST["action"] = LOG_IN;
	$_REQUEST["user"] = $_COOKIE["toby_email"];
	$_REQUEST["pass"] = $_COOKIE["toby_pass"];
}

include("globals.php");

// If the user has never logged in here before, get their information.
if ($_REQUEST["action"] == "initial_setup"){
	// Attempt to determine their e-mail host.
	//$foundhost = determine_host($_SESSION["toby"]["email_address"],$_SESSION["toby"]["pass"]);
	
	$output .= '
		<html>
			<head>
				<title>'.APP_TITLE.'</title>
				<link rel="stylesheet" type="text/css" href="style.css" />
			</head>
			<body>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<div id="login" style="margin: 0;">
					<table cellspacing="2" style="width: 100%;">
						<tr>
							<td colspan="3">
								<p>'.FIRST_LOGIN_INTRO.'</p>
							</td>
						</tr>
						<tr>
							<td class="formlabel" style="width: 40%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px; vertical-align: middle;">
								<label for="realname">'.FULL_NAME_QUESTION.'</label>
							</td>
							<td colspan="2" class="forminput" style="width: 30%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px;">
								<input type="text" name="realname" />
							</td>
						</tr>';
	
	// If the host information couldn't be determined, ask the user.
	if (!$foundhost) {
		$output .= '
						<tr>
							<td class="formlabel" style="width: 40%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px; vertical-align: middle;">
								<label for="host">'.MAIL_HOST_QUESTION.'</label>
							</td>
							<td colspan="2" class="forminput" style="width: 30%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px;">
								<input type="text" name="host" />
							</td>
						</tr>';
	}
	else{
		$output .= '
			<input type="hidden" name="host" value="'.$foundhost["host"].'" />
			<input type="hidden" name="user" value="'.$foundhost["user"].'" />
			<input type="hidden" name="protocol" value="'.$foundhost["protocol"].'" />
			<input type="hidden" name="port" value="'.$foundhost["port"].'" />';
	}
	
	$output .= '		<tr>
							<td class="formlabel" style="width: 40%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px; vertical-align: middle;"><label for="save_sent">'.SAVE_SENT_QUESTION.'</p></label></td>
							<td class="forminput" style="width: 30%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px;"><input type="radio" name="save_sent" id="save_sent" value="1" /> '.YES.'</td>
							<td class="forminput" style="width: 30%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px;"><input type="radio" name="save_sent" id="save_sent" value="0" /> '.NO.'</td>
						</tr>
						<tr>
							<td class="formlabel" style="width: 40%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px; vertical-align: middle;"><label for="save">'.SAVE_INCOMING_QUESTION.'</p>
								<p><i>'.SAVE_INCOMING_CAUTION.'</i></p></label></td>
							<td class="forminput" style="width: 30%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px;"><input type="radio" name="save" value="1" /> '.YES.'</td>
							<td class="forminput" style="width: 30%; border-width: 1px; border-style: solid; border-color: #aaaadd; padding: 5px;"><input type="radio" name="save" value="0" /> '.NO.'</td>
						</tr>
						<tr>
							<td colspan="3" style="text-align: center;">
								<br />
								<input type="submit" name="'.str_replace(" ","_",CONTINUE_BUTTON).'" value="'.CONTINUE_BUTTON.'" />
							</td>
						</tr>
					</table>
				</div>
			</form>
		</body>
	</html>';
}
// If they had never logged in before and have now submitted their information,
// add the user to the database.
elseif ($_REQUEST["action"] == "Continue"){
	// Get the parts of the user's address.
	$misc = explode("@",$_SESSION["toby"]["email_address"]);
	
	// Add the user's email address to the user's table
	$query = "INSERT INTO `email_users` (`email_address`) VALUES ('".$_SESSION["toby"]["email_address"]."')";
	$result = run_query($query);
	
	// Set up the session variables.
	$_SESSION["toby"]["userid"] = mysql_insert_id();
	$_SESSION["toby"]["texthost"] = $_REQUEST["host"];
	$_SESSION["toby"]["save"] = ((int) $_REQUEST["save"]);
	$_SESSION["toby"]["realname"] = $_REQUEST["realname"];
	$_SESSION["toby"]["protocol"] = $_REQUEST["protocol"];
	$_SESSION["toby"]["user"] = $_REQUEST["user"];
	
	// If the username or port has not been determined, figure it out here
	if ((!$_REQUEST["user"]) || (!$_REQUEST["port"])){
		if (!$_REQUEST["user"]){
			$users = array($_SESSION["toby"]["email_address"],$misc[0]);
		}
		else{
			$users = array($_REQUEST["user"]);
		}
		
		if (!$_REQUEST["port"]){
			$ports = array(array(110,"/pop3"),array(143,""));
		}
		else{
			$ports = array($_REQUEST["port"],$_REQUEST["protocol"]);
		}
		
		// Try each combination of user and port until a successful connection.
		foreach ($users as $user){
			foreach($ports as $port){
				if (@imap_open("{".$_SESSION["toby"]["texthost"].":".$port[0].$port[1]."}INBOX", $user, $_SESSION["toby"]["pass"])){
					$usertype = strpos($user, "@") ? 1 : 0;
					
					$_SESSION["toby"]["user"] = $user;
					$_SESSION["toby"]["protocol"] = $port[1];
					$_SESSION["toby"]["port"] = $port[0];
					
					break 2;
				}
			}
		}
	}
	
	if ($_SESSION["toby"]["protocol"] == '/pop3'){
		$_SESSION["toby"]["host"] = gethostbyname($_SESSION["toby"]["texthost"]);
		$_SESSION["toby"]["port"] = 110;
	}
	else{
		$_SESSION["toby"]["host"] = $_SESSION["toby"]["texthost"];
		$_SESSION["toby"]["port"] = 143;
	}
	
	// Add the host to the hosts table
	$query = "INSERT INTO `email_hosts` 
				(`name`,
				 `domain`,
				 `protocol`,
				 `port`,
				 `usertype`) 
				VALUES 
				 ('".$_SESSION["toby"]["texthost"]."',
				  '".$misc[1]."',
				  '".$_SESSION["toby"]["protocol"]."',
				  ".$_SESSION["toby"]["port"].",
				  ".((int) $usertype)."
				  )";
	$result = @mysql_query($query);
	
	// Get the host id to add to the user's entry in the users table.
	$query = "SELECT `id` FROM `email_hosts` WHERE `name`='".$_SESSION["toby"]["texthost"]."' AND `domain`='".$misc[1]."'";
	$result = run_query($query);
	$host_id = mysql_result($result, 0, 'id');
	
	// Create a sent folder for this user.
	$query = "INSERT INTO `email_folders` (`user`,`folder_name`) VALUES (".$_SESSION["toby"]["userid"].",'Sent')";
	$result = run_query($query);
	
	// Get the sent folder's id
	$_SESSION["toby"]["sent_folder"] = mysql_insert_id();
	
	// Add the rest of the user information to the users table
	$query = "UPDATE 
					`email_users` 
				SET 
					`sent_folder`=".$_SESSION["toby"]["sent_folder"].",
					`host`=".((int) $host_id).",
					`save_sent`=".((int)$_REQUEST["save_sent"]).",
					`save_messages`=".$_SESSION["toby"]["save"].",
					`username`='".$_SESSION["toby"]["user"]."',
					`realname`='".$_SESSION["toby"]["realname"]."'
				WHERE 
					`id`=".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	header("Location: " . $_SERVER["PHP_SELF"]);
	exit;
}
else{
	// This is the wrapper code for the main part of the program.
	$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
		<html>
			<head>
				<title>'.APP_TITLE.'</title>
				<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
				<script type="text/javascript">
					function toppify_client(){
						if (self != top) {
						    top.location = "client.php";
					    }
					}
				</script>
			</head>
			<frameset cols="20%,*" onload="toppify_client();">
			  	<frame src="'.$folderpage.'" name="folders">
				<frame src="'.$wrapperpage.'" name="wrapper">
			</frameset>
		</html>';
}

echo $output;
exit;

function determine_host($email,$password){
	$parts = explode("@", $email);
	
	$query = "SELECT * FROM `email_hosts` WHERE `domain`='".$parts[1]."'";
	$result = run_query($query);
	
	if (mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_array($result)){
			if ($row["usertype"] == 1){
				$user = $email;
			}
			else{
				$user = $parts[0];
			}
			
			if (@imap_open("{".$row["name"].":".$row["port"].$row["protocol"]."}INBOX", $user, $password)){
				return array("host"=>$row["name"],"protocol"=>$row["protocol"],"port"=>$row["port"],"user"=>$user);
			}
		}
	}
	else{
		$subdomains = array("www.","mail.","pop.","email.","imap.","",$parts[0]);
		$users = array($parts[0],$email);
		$protocols = array(array(143,""),array("110",'/pop3'));
		
		$hosts = array($parts[1]);
		
		foreach ($subdomains as $subdomain){
			foreach ($hosts as $host){
				$newhost = $subdomain . $host;
				foreach ($users as $user){
					foreach ($protocols as $protocol){
						if (@imap_open("{".$newhost.":".$protocol[0].$protocol[1]."}INBOX", $user, $password)){
							return array("host"=>$newhost,"protocol"=>$protocol[1],"port"=>$protocol[0],"user"=>$user);
						}
					}
				}
			}
		}
		
		$tracert = exec("traceroute ".escapeshellcmd($parts[1]));
		$tracert = str_replace("  "," ",$tracert);
		$tracert = explode(" ", $tracert);
		$tracert = trim($tracert[1]);
		
		$hosts = array($tracert);
		
		foreach ($subdomains as $subdomain){
			foreach ($hosts as $host){
				$newhost = $subdomain . $host;
				foreach ($users as $user){
					foreach ($protocols as $protocol){
						if (@imap_open("{".$newhost.":".$protocol[0].$protocol[1]."}INBOX", $user, $password)){
							if (strpos($user, "@")){
								$usertype = 1;
							}
							else{
								$usertype = 0;
							}
							
							$query = "INSERT INTO `email_hosts` (`domain`,`name`,`protocol`,`port`,`usertype`)
										VALUES ('".$parts[1]."','".$newhost."','".$protocol[1]."',".$protocol[0].",".$usertype.")";
							$result = run_query($query);
							
							return array("host"=>$mewhost,"protocol"=>$protocol[1],"port"=>$port1,"user"=>$user);
						}
					}
				}
			}
		}
	}
	
	return false;
}

?>