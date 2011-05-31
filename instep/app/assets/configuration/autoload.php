<?php

define('INSTEP_DATABASE', 'instep');

function getRuntimeObjects(){
	
	$userObject = new userWrapper();
	
	return array($userObject);
}

// This function auto loads classes.
function __autoload($class_name) {

	if(stristr($class_name, 'PEAR') !== false){
		$path = str_replace("_", "/", $class_name);
		if(strtolower($path) != "pear/error") include_once($class_name . ".php");
	} else {	
		include_once($class_name . ".class.php");
	
		if (!class_exists($class_name, false)) {
	   		trigger_error("Unable to load class: $class_name", E_USER_WARNING);
	  	}
	}

}

?>