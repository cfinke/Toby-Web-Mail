<?php

// Installation file.

error_reporting(E_ALL ^ E_NOTICE);

$langs = array("en"=>"English","es"=>"Español");
$path = $_REQUEST["directory"];
$errors = array();
$config_errors = array();

$writetoconfig = '<?php

$database_host = "'.stripslashes($_REQUEST["database_host"]).'";
$database_user = "'.stripslashes($_REQUEST["database_user"]).'";
$database_password = "'.stripslashes($_REQUEST["database_password"]).'";
$database_name = "'.stripslashes($_REQUEST["database_name"]).'";

$admin_email = "'.stripslashes($_REQUEST["email"]).'";

$temp_directory = "'.$_REQUEST["temp_directory"].'";

$default_lang = "'.$_REQUEST["lang"].'";

if (isset($_SESSION["toby"]["lang"])){
	include("lang/".$_SESSION["toby"]["lang"].".php");
}
else{
	include("lang/".$default_lang.".php");
}

?>';

$output = '
	<html>
		<head>
			<title>Toby Web Mail Installer</title>
			<link rel="stylesheet" type="text/css" href="style.css" />
		</head>
		<body style="margin: 0; padding: 0;">
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<h1>Install Toby Web Mail</h1>';

if ($_REQUEST["action"] == "install"){
	// First, check for missing data.
	if(strlen(trim($_REQUEST["lang"])) == 0){
		$errors[] = 'Please specify a default language.';
	}
	if(strlen(trim($_REQUEST["email"])) == 0){
		$errors[] = 'Please enter the administrator\'s e-mail address.';
	}
	if (strlen(trim($_REQUEST["database_host"])) == 0){
		$errors[] = 'Please enter the name of your MySQL host.';
	}
	if (strlen(trim($_REQUEST["database_user"])) == 0){
		$errors[] = 'Please enter the MySQL username.';
	}
	if (strlen(trim($_REQUEST["database_name"])) == 0){
		$errors[] = 'Please enter the MySQL database name.';
	}
	if (strlen(trim($_REQUEST["database_password"])) == 0){
		$errors[] = 'Please enter the MySQL password.';
	}
	if (strlen(trim($_REQUEST["directory"])) == 0){
		$errors[] = 'Please enter the full path to the Toby installation directory.';
	}
	if (strlen(trim($_REQUEST["temp_directory"])) == 0){
		$errors[] = 'Please enter the full path to the temporary file directory.';
	}
	
	if (count($errors) == 0){
		if(!@mysql_connect($_REQUEST["database_host"],$_REQUEST["database_user"],$_REQUEST["database_password"])){
			$errors[] = 'Toby could not connect to the MySQL host with the information you provided.';
		}
		elseif(!mysql_select_db($_REQUEST["database_name"])){
			$errors[] = 'Toby connected to the MySQL host, but could not find the database '.$_REQUEST["database_name"].'.';
		}
		else{
			if (!$_REQUEST["overwrite_tables"]){
				$tables = array("email","email_hosts","email_users","email_overflow","email_folders","email_transfer","email_address_book");
				
				foreach ($tables as $table){
					$query = "SHOW TABLES LIKE '".$table."'";
					$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
					
					if (mysql_num_rows($result) > 0){
						$errors[] = "The table `".$table."` already exists in the MySQL database ".$_REQUEST["database_name"].".";
					}
				}
			}
		}
	}
	
	if (count($errors) == 0){
		$handle = @fopen($path . "config.php","w");
		if ($handle){
			fwrite($handle, $writetoconfig);
			fclose($handle);
		}
		else{
			@chmod($path . "config.php", 0666);
			
			$handle = @fopen($path . "config.php","w");
			
			if ($handle){
				fwrite($handle, $writetoconfig);
				fclose($handle);
			}
			else{
				$config_errors[] = "Toby could not write the config.php file.  Either make this file writable by the Web server and click 'Try Again', or replace the contents of the current config.php file with the following code:<br /><pre>" . htmlentities($writetoconfig) . "</pre>If you choose to overwrite the file manually, do so, and then delete the files install.php and upgrade.php, if they exist.  Don't forget to change the permissions back on config.php after you overwrite it.";
			}
		}
	}
	
	if (count($errors) == 0){
		$query = "DROP TABLE `email` ,
			`email_folders` ,
			`email_hosts` ,
			`email_overflow` ,
			`email_transfer` ,
			`email_users`,
			`email_address_book`";
		$result = @mysql_query($query);
		
		$query = "
			CREATE TABLE `email` (
			  `id` int(11) NOT NULL auto_increment,
			  `user` int(64) NOT NULL default '0',
			  `share` int(11) NOT NULL default '0',
			  `headers` text NOT NULL,
			  `message` longtext NOT NULL,
			  `num_attachments` int(11) NOT NULL default '0',
			  `has_html` int(1) NOT NULL default '0',
			  `Date` varchar(255) NOT NULL default '',
			  `Return-Path` varchar(255) NOT NULL default '',
			  `From` varchar(255) NOT NULL default '',
			  `To` text NOT NULL,
			  `Cc` text NOT NULL,
			  `Subject` varchar(255) NOT NULL default '',
			  `niceDate` varchar(14) NOT NULL default '0',
			  `Reply-To` varchar(255) NOT NULL default '',
			  `Message-ID` varchar(255) NOT NULL default '', 
 			  `In-Reply-To` varchar(255) NOT NULL default '',
			  `Content-Type` varchar(255) NOT NULL default '',
			  `folder` int(11) NOT NULL default '0',
			  `seen` tinyint(1) NOT NULL default '0',
			  `temp` tinyint(1) NOT NULL default '0',
			  `sent` tinyint(1) NOT NULL default '0', 
			  UNIQUE KEY `id` (`id`),
			  KEY `From` (`user`,`From`),
			  KEY `folder` (`folder`)
			) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		$query = "
			CREATE TABLE `email_folders` (
				  `id` int(11) NOT NULL auto_increment,
				  `parent_id` int(11) NOT NULL default '0',
				  `user` int(11) NOT NULL default '0',
				  `folder_name` varchar(64) NOT NULL default '',
				  UNIQUE KEY `id` (`id`),
				  KEY `user` (`user`,`parent_id`)
				) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		$query = "
			CREATE TABLE `email_hosts` (
			  `id` int(11) NOT NULL auto_increment,
			  `domain` varchar(64) NOT NULL default '',
			  `name` varchar(128) NOT NULL default '',
			  `display_name` varchar(128) NOT NULL default '',
			  `protocol` varchar(5) NOT NULL default '',
			  `port` int(4) NOT NULL default '0',
			  `usertype` int(1) NOT NULL default '0',
			  UNIQUE KEY `id` (`id`),
			  UNIQUE KEY `domain` (`domain`,`name`)
			) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		$query = "
			CREATE TABLE `email_overflow` (
			  `key` int(11) NOT NULL default '0',
			  `part_id` int(11) NOT NULL default '0',
			  `part` longtext NOT NULL,
			  `user` int(11) NOT NULL default '0',
			  `share` int(11) NOT NULL default '0',
			  `temp` int(1) NOT NULL default '0',
			  KEY `key` (`key`)
			) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		$query = "
			CREATE TABLE `email_transfer` (
			  `id` int(11) NOT NULL auto_increment,
			  `share` int(1) NOT NULL default '0',
			  `from` int(11) NOT NULL default '0',
			  `to` varchar(128) NOT NULL default '',
			  `message` varchar(255) NOT NULL default '',
			  UNIQUE KEY `id` (`id`)
			) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		$query = "
			CREATE TABLE `email_users` (
			  `id` int(11) NOT NULL auto_increment,
			  `username` varchar(128) NOT NULL default '',
			  `host` int(11) NOT NULL default '0',
			  `email_address` varchar(128) NOT NULL default '',
			  `realname` varchar(128) NOT NULL default '',
			  `save_messages` tinyint(1) NOT NULL default '0',
			  `save_sent` tinyint(1) NOT NULL default '1',
			  `sent_folder` int(11) NOT NULL default '0',
			  `compose_type` ENUM( 'text', 'html' ) NOT NULL DEFAULT 'text',
			  `lang` VARCHAR( 8 ) NOT NULL DEFAULT 'en',
  			  `timezone` VARCHAR( 8 ) NOT NULL DEFAULT '+0000',
  			  `refresh_interval` VARCHAR(4) NOT NULL DEFAULT '10',
			  UNIQUE KEY `id` (`id`),
			  UNIQUE KEY `username` (`email_address`,`host`)
			) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		$query = "
			CREATE TABLE `email_address_book` (
			  `id` int(11) NOT NULL auto_increment,
			  `userid` int(11) NOT NULL default '0',
			  `name` varchar(64) NOT NULL default '',
			  `email_address` varchar(128) NOT NULL default '',
			  UNIQUE KEY `id` (`id`)
			) TYPE=MyISAM";
		$result = mysql_query($query) or die(mysql_error() . '<br />' .$query);
		
		if (count($config_errors) == 0){
			if (is_file($path . "install.php")) @unlink($path . "install.php");
			if (is_file($path . "upgrade.php")) @unlink($path . "upgrade.php");
			
			header("Location: index.php");
		}
		else{
			$set_config_error = true;
			
			$output .= '
				<input type="hidden" name="action" value="try_again" />
				<input type="hidden" name="database_host" value="'.stripslashes($_REQUEST["database_host"]).'" />
				<input type="hidden" name="database_user" value="'.stripslashes($_REQUEST["database_user"]).'" />
				<input type="hidden" name="database_password" value="'.stripslashes($_REQUEST["database_password"]).'" />
				<input type="hidden" name="database_name" value="'.stripslashes($_REQUEST["database_name"]).'" />
				<input type="hidden" name="email" value="'.stripslashes($_REQUEST["email"]).'" />
				<input type="hidden" name="temp_directory" value="'.$_REQUEST["temp_directory"].'" />
				<input type="hidden" name="lang" value="'.$_REQUEST["lang"].'" />
				<p>Toby found the following errors:</p><ul class="error">';
			
			foreach($config_errors as $error){
				$output .= '<li>'.$error.'</li>';
			}
			
			$output .= '</ul>
						<table>
							<tr>
								<td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Try Again" /></td>
							</tr>
						</table>';
		}
	}
}

if($_REQUEST["action"] == "try_again"){
	$handle = @fopen($path . "config.php","w");
	if ($handle){
		fwrite($handle, $writetoconfig);
		fclose($handle);
	}
	else{
		@chmod($path . "config.php", 0666);
		
		$handle = @fopen($path . "config.php","w");
		
		if ($handle){
			fwrite($handle, $writetoconfig);
			fclose($handle);
		}
		else{
			$config_errors[] = "Toby could not write the config.php file.  Either make this file writable by the Web server and click 'Try Again', or replace the contents of the current config.php file with the following code:<br /><pre>" . htmlentities($writetoconfig) . "</pre>If you choose to overwrite the file manually, do so, and then delete the files install.php and upgrade.php, if they exist.  Don't forget to change the permissions back on config.php after you overwrite it.";
		}
	}
	
	if (count($config_errors) == 0){
		if (is_file($path . "install.php")) unlink($path . "install.php");
		if (is_file($path . "upgrade.php")) unlink($path . "upgrade.php");
		
		header("Location: index.php");
	}
	else{
		$output .= '
			<input type="hidden" name="action" value="try_again" />
			<input type="hidden" name="database_host" value="'.stripslashes($_REQUEST["database_host"]).'" />
			<input type="hidden" name="database_user" value="'.stripslashes($_REQUEST["database_user"]).'" />
			<input type="hidden" name="database_password" value="'.stripslashes($_REQUEST["database_password"]).'" />
			<input type="hidden" name="database_name" value="'.stripslashes($_REQUEST["database_name"]).'" />
			<input type="hidden" name="email" value="'.stripslashes($_REQUEST["email"]).'" />
			<input type="hidden" name="temp_directory" value="'.$_REQUEST["temp_directory"].'" />
			<input type="hidden" name="lang" value="'.$_REQUEST["lang"].'" />
			<p>Toby found the following errors:</p><ul class="error">';
		
		foreach($config_errors as $error){
			$output .= '<li>'.$error.'</li>';
		}
		
		$output .= '</ul>
					<table>
						<tr>
							<td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Try Again" /></td>
						</tr>
					</table>';
	}
}
elseif(!$set_config_error){
	$database_host = ($_REQUEST["action"] != "") ? $_REQUEST["database_host"] : 'localhost';
	$tmp_directory = ($_REQUEST["action"] != "") ? stripslashes($_REQUEST["temp_directory"]) : '/tmp/';
	$directory = ($_REQUEST["action"] != "") ? $_REQUEST["directory"] : $_SERVER["DOCUMENT_ROOT"].str_replace("install.php","",$_SERVER["PHP_SELF"]);
	$checked = ($_REQUEST["overwrite_tables"]) ? ' checked="true"' : '';
	
	if (count($errors) > 0){
		$output .= '<p>Toby found the following errors:</p><ul class="error">';
		
		foreach($errors as $error){
			$output .= '<li>'.$error.'</li>';
		}
		
		$output .= '</ul>';
	}
	
	$output .= '	<input type="hidden" name="action" value="install" />
					<table>
						<tr>
							<td class="formlabel"><label for="language">Default interface language:</label></td>
							<td>
								<select name="lang" id="lang">
									<option value="">Select a language.</option>';
	
	foreach($langs as $key => $lang){
		$output .= '<option value="'.$key.'"';
		if ($_REQUEST["lang"] == $key) $output .= ' selected="selected"';
		$output .= '>'.$lang.'</option>';
	}
	
	$output .= '			</td>
						</tr>
						<tr>
							<td class="formlabel"><label for="email">Administrator\'s E-mail:</label></td>
							<td><input type="text" name="email" id="email" value="'.$_REQUEST["email"].'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="database_host">MySQL host:</label></td>
							<td><input type="text" name="database_host" id="database_host" value="'.$database_host.'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="database_user">MySQL Username:</label></td>
							<td><input type="text" name="database_user" id="database_user" value="'.$_REQUEST["database_user"].'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="database_password">MySQL Password:</label></td>
							<td><input type="text" name="database_password" id="database_password" value="'.$_REQUEST["database_password"].'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="database_name">MySQL Database:</label></td>
							<td><input type="text" name="database_name" id="database_name" value="'.$_REQUEST["database_name"].'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="directory"><i>Full Path</i> to Toby Installation Directory:</label></td>
							<td><input type="text" name="directory" id="directory" value="'.$directory.'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="temp_directory">Temporary File Directory:</label></td>
							<td><input type="text" name="temp_directory" id="temp_directory" value="'.$tmp_directory.'" /></td>
						</tr>
						<tr>
							<td class="formlabel"><label for="overwrite_tables">Overwrite tables of the same name:</label></td>
							<td><input type="checkbox" name="overwrite_tables" id="overwrite_tables" value="1"'.$checked.' /></td>
						</tr>
						<tr>
							<td class="formlabel"></td>
							<td><input type="submit" name="submit" id="submit" value="Install" /></td>
						</tr>
					</table>';
}

$output .= '
			</form>
		</body>
	</html>';

echo $output;
exit;

?>