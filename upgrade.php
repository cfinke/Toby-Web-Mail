<?php

$path = $_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"];
$path = str_replace("upgrade.php","config.php",$path);

include("config.php");
include("functions.php");

connect_to_database();

$write_to_config = '<?php

$database_host = "'.$database_host.'";
$database_user = "'.$database_user.'";
$database_password = "'.$database_password.'";
$database_name = "'.$database_name.'";

$admin_email = "'.$admin_email.'";

$default_lang = "'.$_REQUEST["language"].'";

if (isset($_SESSION["toby"]["lang"])){
	include("lang/".$_SESSION["toby"]["lang"].".php");
}
else{
	include("lang/".$default_lang.".php");
}

?>';

$errors = array();

if ($_REQUEST["action"] == "do_upgrade"){
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
			
			@system("chmod 777 ".$path);
			
			$handle = @fopen($path,"w");
			if ($handle){
				fwrite($handle, $write_to_config);
				fclose($handle);
				@system("chmod 755 ".$path);
			}
			else{
				$errors[] = "Toby could not write the config.php file.  Either chmod this file to be writable by the Web server, or replace the contents of the current config.php file with the following code:<br /><pre>" . htmlentities($write_to_config) . "</pre>If you choose to overwrite the file manually, do so, and then delete the files install.php and upgrade.php, if they exist.  Don't forget to change the permissions back on config.php after you overwrite it. Otherwise, you can change the permissions on config.php and have the script attempt to overwrite it by clicking the 'Try Again' button.";
			}
			
			break;
		case '0.4':
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
			
			break;
		case '0.4.1':
			$query = "ALTER TABLE `email_users` ADD `timezone` VARCHAR(8) DEFAULT '+0000' NOT NULL";
			$result = @mysql_query($query);
			
			break;
	}
	
	## END OF CHANGES
	
	if (count($errors) == 0){
		$install_file = $_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"];
		$install_file = str_replace("upgrade.php","install.php", $install_file);
		
		unlink($_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"]);
		if (is_file($install_file)) unlink($install_file);
		
		header("Location: index.php");
	}
}
elseif($_REQUEST["action"] == "try_again"){
	@system("chmod 777 ".$path);
	$handle = @fopen($path,"w");
	if ($handle){
		fwrite($handle, $write_to_config);
		fclose($handle);
		@system("chmod 755 ".$path);
	}
	else{
		$errors[] = "Toby could not write the config.php file.  Either chmod this file to be writable by the Web server, or replace the contents of the current config.php file with the following code:<br /><pre>" . htmlentities($write_to_config) . "</pre>If you choose to overwrite the file manually, do so, and then delete the files install.php and upgrade.php, if they exist.  Don't forget to change the permissions back on config.php after you overwrite it.  Otherwise, you can change the permissions on config.php and have the script attempt to overwrite it by clicking the 'Try Again' button.";
	}
	
	if (count($errors) == 0){
		$install_file = $_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"];
		$install_file = str_replace("upgrade.php","install.php", $install_file);
		
		unlink($_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"]);
		if (is_file($install_file)) unlink($install_file);
		
		header("Location: index.php");
	}
}

$output .= '
	<html>
		<head>
			<title>Toby Web Mail Upgrade</title>
			<link rel="stylesheet" type="text/css" href="style.css" />
		</head>
		<body>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<fieldset>
					<legend>Upgrade</legend>';

if (count($errors) > 0){
	$output .= '<input type="hidden" name="action" value="try_again" />
		<p>Toby found the following errors:</p><ul class="error">';
	
	foreach($errors as $error){
		$output .= '<li>'.$error.'</li>';
	}
	
	$output .= '</ul>';
	
	$output .= '<table>
					<tr>
						<td>To what language should Toby default?</td>
						<td>
							<select name="language">
								<option value="en">English</option>
								<option value="es">Espanol</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Try Again" /></td>
					</tr>
				</table>';
}
else{
	$output .= '<p>Before running this script, make config.php writable by the server if you are updating from a version before 4.0.</p>';
	
	$output .= '
						<input type="hidden" name="action" value="do_upgrade" />
						<table>
							<tr>
								<td>From which version of Toby Web Mail are you upgrading?  (If you don\'t know, select 0.1.)</td>
								<td>
									<select name="old_version">
										<option value="0.4">0.4.1</option>
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
								<td>To what language should Toby default?</td>
								<td>
									<select name="language">
										<option value="en">English</option>
										<option value="es">Espanol</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center;"><input type="submit" name="submit" value="Submit" /></td>
							</tr>
						</table>';
}

$output .= '
					</fieldset>
				</form>
			</body>
		</html>';

echo $output;
exit;

?>