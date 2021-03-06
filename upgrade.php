<?php

// Upgrade file.

error_reporting(E_ALL ^ E_NOTICE);

include("config.php");
include("functions.php");

$langs = array("en"=>"English","es"=>"Español","de"=>"Deutsch");
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
			<title>Toby Web Mail Upgrade</title>
			<link rel="stylesheet" type="text/css" href="style.css" />
		</head>
		<body style="margin: 0; padding: 0;">
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<h1>Upgrade Toby Web Mail</h1>';

foreach($_REQUEST as $key => $value) $_REQUEST[$key] = stripslashes($value);

if ($_REQUEST["action"] == "upgrade"){
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
	
	// Now check for incorrect data.
	if (count($errors) == 0){
		if(!@mysql_connect($_REQUEST["database_host"],$_REQUEST["database_user"],$_REQUEST["database_password"])){
			$errors[] = 'Toby could not connect to the MySQL host with the information you provided.';
		}
		elseif(!mysql_select_db($_REQUEST["database_name"])){
			$errors[] = 'Toby connected to the MySQL host, but could not find the database '.$_REQUEST["database_name"].'.';
		}
	}
	
	// Make the appropriate changes, depending on the old version.	
	if (count($errors) == 0){
		mysql_connect($_REQUEST["mysql_host"],$_REQUEST["database_user"],$_REQUEST["database_password"]);
		mysql_select_db($_REQUEST["database_name"]);
		
		switch($_REQUEST["old_version"]){
			case '0.1':
			case '0.1.1':
				## Changes introduced in 0.2	
				$query = "ALTER TABLE `email` DROP `niceTo`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `niceFrom`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `created`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `modified`";
				$result = @mysql_query($query);
			case '0.2.0':
				## Changes introduced in 0.2.1	
				$query = "ALTER TABLE `email_overflow` DROP `modified`";
				$result = @mysql_query($query);
			case '0.2.1':
				## Changes inroduced in 0.3	
				$query = "ALTER TABLE `email_users` ADD `compose_type` ENUM( 'text', 'html' ) DEFAULT 'text' NOT NULL";
				$result = @mysql_query($query);
			case '0.3.0':
				## Changes introduced in 0.3.1
				
				$query = "ALTER TABLE `email` DROP `Mime-Version`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `Bcc`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `Content-Transfer-Encoding`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `Envelope-to`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP INDEX `niceFrom`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` CHANGE `folder` `folder` INT NOT NULL";
				$result = @mysql_query($query);
			case '0.3.1':
				## Changes introduced in 0.4
				
				$query = "ALTER TABLE `email_users` ADD `lang` VARCHAR( 8 ) DEFAULT 'en' NOT NULL";
				$result = @mysql_query($query);
			case '0.4':
				## Changes introduced in 0.4.1
				$query = "ALTER TABLE `email` DROP INDEX `Content-Type`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `Content-Disposition`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` DROP `spam`";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` ADD `Message-ID` VARCHAR(255) DEFAULT '' NOT NULL";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` ADD `In-Reply-To` VARCHAR(255) DEFAULT '' NOT NULL";
				$result = @mysql_query($query);
			case '0.4.1':
				## Changes introduces in 0.4.2
				$query = "ALTER TABLE `email_users` ADD `timezone` VARCHAR(8) DEFAULT '+0000' NOT NULL";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email_users` ADD `refresh_interval` VARCHAR(4) DEFAULT '10' NOT NULL";
				$result = @mysql_query($query);
			case '0.4.2':
				## Changes introduced in 0.5
				
				$query = "ALTER TABLE `email` ADD INDEX ( `Message-ID` ) ";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` ADD INDEX ( `In-Reply-To` ) ";
				$result = @mysql_query($query);
				
				$query = "ALTER TABLE `email` ADD `sent` tinyint(1) NOT NULL default '0'";
				$result = @mysql_query($query);
				
				// Attempt to write the config file.
				@chmod($path."config.php", 0777);
				
				$handle = @fopen($path."config.php","w");
				
				if ($handle){
					fwrite($handle, $writetoconfig);
					fclose($handle);
					@chmod($path."config.php", 0755);
					
					$config_written = true;
				}
				else{
					$config_errors[] = "Toby could not write the config.php file.  Either make this file writable by the Web server and click 'Try Again', or replace the contents of the current config.php file with the following code:<br /><pre>" . htmlentities($writetoconfig) . "</pre>If you choose to overwrite the file manually, do so, and then delete the files install.php and upgrade.php, if they exist.  Don't forget to change the permissions back on config.php after you overwrite it.";
				}
				
				break;
		}
		
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
				<p>Toby found the following errors:</p>
				<ul class="error">';
			
			foreach($config_errors as $error){
				$output .= '<li>'.$error.'</li>';
			}
			
			$output .= '
				</ul>
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
		if (is_file($path . "install.php")) @unlink($path . "install.php");
		if (is_file($path . "upgrade.php")) @unlink($path . "upgrade.php");
		
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
			<p>Toby found the following errors:</p>
			<ul class="error">';
		
		foreach($config_errors as $error){
			$output .= '<li>'.$error.'</li>';
		}
		
		$output .= '
			</ul>
				<table>
					<tr>
						<td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Try Again" /></td>
					</tr>
				</table>';
	}
}
elseif(!$set_config_error){
	$mysql_host = ($_REQUEST["action"] != "") ? $_REQUEST["database_host"] : 'localhost';
	$tmp_directory = ($_REQUEST["action"] != "") ? stripslashes($_REQUEST["temp_directory"]) : '/tmp/';
	$directory = ($_REQUEST["action"] != "") ? $_REQUEST["directory"] : str_replace("upgrade.php","",$_SERVER["PATH_TRANSLATED"]);
	
	if (count($errors) > 0){
		$output .= '
			<p>Toby found the following errors:</p>
			<ul class="error">';
		
		foreach($errors as $error){
			$output .= '<li>'.$error.'</li>';
		}
		
		$output .= '</ul>';
	}
	
	$output .= '	<input type="hidden" name="action" value="upgrade" />
					<table>
						<tr>
							<td>From which version of Toby Web Mail are you upgrading?  (If you don\'t know, select 0.1.)</td>
							<td>
								<select name="old_version">
									<option value="0.4.2">0.4.2</option>
									<option value="0.4.1">0.4.1</option>
									<option value="0.4">0.4</option>
									<option value="0.3.1">0.3.1</option>
									<option value="0.3">0.3</option>
									<option value="0.2.1">0.2.1</option>
									<option value="0.2">0.2</option>
									<option value="0.1.1">0.1.1</option>
									<option value="0.1" selected="selected">0.1</option>
								</select>
							</td>
						</tr>					
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
							<td><input type="text" name="database_host" id="database_host" value="'.$mysql_host.'" /></td>
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
							<td class="formlabel"></td>
							<td><input type="submit" name="submit" id="submit" value="Upgrade" /></td>
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