<?php
// Define system-wide error reporting
error_reporting(E_ALL);

// Location of conf.php in infolio.
define("INSTEP_INFOLIO_DIR", "/var/www/dev/InStep/");
define("INSTEP_INFOLIO_CONF", INSTEP_INFOLIO_DIR . "system/conf.php");

define('INSTEP_SYS_DEFAULTCNTRLR', 'home');

define('INSTEP_SYS_ROOTDIR', "/var/www/dev/InStep/instep/");
define('INSTEP_SYS_REALBASEURL', 'http://instep.devx.co.uk/');
define('INSTEP_SYS_INCLUDEURL', INSTEP_SYS_REALBASEURL . 'instep/');
define('INSTEP_SYS_MATCHBASEURL', '/http:\/\/instep\.devx\.co\.uk\/.*\/?instep/i');
define('INSTEP_SYS_CLASSDIR', INSTEP_SYS_ROOTDIR . "app/system/classes/");
define('INSTEP_SYS_SYSDIR', INSTEP_SYS_ROOTDIR . "app/system/");
define('INSTEP_SYS_ASSETDIR', INSTEP_SYS_ROOTDIR . "app/assets/");

define('INSTEP_SYS_INCLUDEPATHS', serialize(array(
	INSTEP_SYS_CLASSDIR,
	INSTEP_SYS_ASSETDIR . "controllers/",
	INSTEP_SYS_ASSETDIR . "classes/",
	INSTEP_INFOLIO_DIR  . "system/"
)));

define('INSTEP_SYS_RESTFORMATS', serialize(array(
	"xml",
	"json"
)));


// Define the root URL
preg_match(INSTEP_SYS_MATCHBASEURL, "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], $matches);

if(count($matches) > 0){
	define('INSTEP_BASEURL', $matches[0] . "/");	
} else {
	define('INSTEP_BASEURL', INSTEP_SYS_INCLUDEURL);	
}

unset($matches);

?>