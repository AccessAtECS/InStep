<?php

class InStep extends db {

	private $m_assets = array();
	
	private $instance;

	const ASSET_JAVASCRIPT = 1;
	const ASSET_CSS = 2;

	public function __construct(){
	
	}
	
	public function db(){
		@parent::__construct(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, INSTEP_DATABASE);
		return $this;
	}

	public function addAsset($path, $type){
		if(is_integer($type) == false) throw new Exception("Asset type must be an integer.");
		// Add an asset to formatter	
		switch($type){
			
			case InStep::ASSET_JAVASCRIPT:
				$includeType = "JS";
			break;
			
			case InStep::ASSET_CSS:
				$includeType = "CSS";
			break;
		
		
		}
		array_push($this->m_assets, array("path" => $path, "type" => $includeType));
	}

	public function setupSuperview(view &$superview, $class, userWrapper &$userObject){
	
		$this->setSelectedMenuItem($superview, $class);
		$this->setUserDetails($superview, $userObject);
	
		// Add assets, or clear the placeholder if there are no additional assets to load.
		if(count($this->m_assets) == 0){
			$superview->replace("additional-assets", "");
		} else {
			// Add assets, appending to string to replace additional-assets placeholder.
			$assetString = "";
			
			foreach($this->m_assets as $asset){
				// JS asset
				if($asset['type'] == "JS")	$assetString .= "<script type=\"text/javascript\" src=\"{include-url}presentation/{$asset['path']}\"></script>\n";
			}
			
			$superview->replace("additional-assets", $assetString);
		}
	}




	private function setUserDetails(view &$superview, userWrapper &$userObject){
		$userName = $userObject->getName();
		
		if($userName != ""){
			$superview->replace("userName", "Logged in as " . $userName);
		} else {
			$superview->replace("userName", "Not signed in");
		}
	}
	
	public static function sendResponse($code, $message, array $data = array()){
		echo json_encode(array_merge(array("code" => (int)$code, "message" => $message), $data));
		exit;
	}

	public static function shorten($str, $limit = 20){
		return strlen($str) > $limit ? substr($str, 0, $limit - 3) . '...' : $str;
	}	
	
	// Render the top menu items
	public function setSelectedMenuItem(view &$superview, $class){
		switch($class){
		
			case "home":
				$superview->replaceAll(array(
					"navclass-home" => " selected",
					"navclass-compare" => "",
					"navclass-upload" => "",
					"navclass-search" => "",
					"navclass-review" => ""
				));	
			break;
		
			case "compare":
				$superview->replaceAll(array(
					"navclass-compare" => " selected",
					"navclass-home" => "",
					"navclass-upload" => "",
					"navclass-search" => "",
					"navclass-review" => ""
				));	
			break;		

			case "upload":
				$superview->replaceAll(array(
					"navclass-upload" => " selected",
					"navclass-home" => "",
					"navclass-compare" => "",
					"navclass-search" => "",
					"navclass-review" => ""
				));	
			break;	
			
			case "search":
				$superview->replaceAll(array(
					"navclass-upload" => "",
					"navclass-home" => "",
					"navclass-compare" => "",
					"navclass-search" => " selected",
					"navclass-review" => ""
				));	
			break;		
		
			case "review":
				$superview->replaceAll(array(
					"navclass-upload" => "",
					"navclass-home" => "",
					"navclass-compare" => "",
					"navclass-search" => "",
					"navclass-review" => " selected"				
				));
			break;
		
			default:
				$superview->replaceAll(array(
					"navclass-upload" => "",
					"navclass-home" => "",
					"navclass-compare" => "",
					"navclass-search" => "",
					"navclass-review" => ""
				));				
			break;
		
		}
	}


}

?>