<?php

// Installation file.

$langs = array("en"=>"English","es"=>"Español");

if ($_REQUEST["action"] == "install"){
	$errors = array();
	
	$path = $_SERVER["DOCUMENT_ROOT"] . $_REQUEST["directory"];
	
	if($_REQUEST["lang"] == ""){
		$errors[] = 'Please specify a default language.';
	}
	if(!$_REQUEST["email"]){
		$errors[] = 'Please enter the name of your MySQL host.';
	}
	if (!$_REQUEST["mysql_host"]){
		$errors[] = 'Please enter the name of your MySQL host.';
	}
	if (!$_REQUEST["database_name"]){
		$errors[] = 'Please enter the MySQL database name.';
	}
	if (!$_REQUEST["database_user"]){
		$errors[] = 'Please enter the MySQL username.';
	}
	if (!$_REQUEST["database_password"]){
		$errors[] = 'Please enter the MySQL password.';
	}
	if (!$_REQUEST["directory"]){
		$errors[] = 'Please enter the Toby installation directory.';
	}
	
	if (count($errors) == 0){
		if(!@mysql_connect($_REQUEST["mysql_host"],$_REQUEST["database_user"],$_REQUEST["database_password"])){
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
	
	if (!is_writeable($path)){
		$errors[] = "The directory ". $path . " is not currently writable by the server.  Please change the permissions.<br />";
	}
	
	if (count($errors) == 0){
		$writetoconfig = '<?php

$database_host = "'.stripslashes($_REQUEST["mysql_host"]).'";
$database_user = "'.stripslashes($_REQUEST["database_user"]).'";
$database_password = "'.stripslashes($_REQUEST["database_password"]).'";
$database_name = "'.stripslashes($_REQUEST["database_name"]).'";

$admin_email = "'.stripslashes($_REQUEST["email"]).'";

$default_lang = "'.$_REQUEST["lang"].'";

if (isset($_SESSION["toby"]["lang"])){
	include("lang/".$_SESSION["toby"]["lang"].".php");
}
else{
	include("lang/".$default_lang.".php");
}

?>';

		$handle = @fopen($path . "config.php","w");
		if ($handle){
			fwrite($handle, $writetoconfig);
			fclose($handle);
		}
		else{
			$errors[] = "Toby could not write the config.php file.  If this file exists already, delete it.";
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
		
		unlink($path . "install.php");
		unlink($path . "upgrade.php");
		
		header("Location: index.php");
	}
}

$mysql_host = ($_REQUEST["mysql_host"]) ? $_REQUEST["mysql_host"] : 'localhost';
$directory = ($_REQUEST["directory"]) ? $_REQUEST["directory"] : str_replace("install.php","",$_SERVER["PHP_SELF"]);
$checked = ($_REQUEST["overwrite_tables"]) ? ' checked="true"' : '';

$output = '
	<html>
		<head>
			<title>Toby Web Mail Installer</title>
		</head>
		<body>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<fieldset>
					<legend>Install Toby</legend>';

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
						<td class="formlabel"><label for="mysql_host">MySQL host:</label></td>
						<td><input type="text" name="mysql_host" id="mysql_host" value="'.$mysql_host.'" /></td>
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
						<td class="formlabel"><label for="directory">Toby Installation Directory:</label></td>
						<td><input type="text" name="directory" id="directory" value="'.$directory.'" /></td>
					</tr>
					<tr>
						<td class="formlabel"><label for="overwrite_tables">Overwrite tables of the same name:</label></td>
						<td><input type="checkbox" name="overwrite_tables" id="overwrite_tables" value="1"'.$checked.' /></td>
					</tr>
					<tr>
						<td class="formlabel"></td>
						<td><input type="submit" name="submit" id="submit" value="Install" /></td>
					</tr>
				</table>
				</fieldset>
			</form>
		</body>
	</html>';

echo $output;
exit;

?>