<?php

// These are temporary values.  If you can read this line,
// you haven't run the install or upgrade script.

$database_host = "localhost";
$database_user = "localhost";
$database_password = "localhost";
$database_name = "localhost";

$admin_email = "chris@efinke.com";

$temp_directory = "/tmp/";

$default_lang = "en";

if (isset($_SESSION["toby"]["lang"])){
	include("lang/".$_SESSION["toby"]["lang"].".php");
}
else{
	include("lang/".$default_lang.".php");
}

?>