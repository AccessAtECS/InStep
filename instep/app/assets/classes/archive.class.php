<?php

class archive {

	private $originalView;
	private $workingView;

	private $dependencies = array();
	private $userDependencies = array();
	
	private $zipPath;
	
	private $buildPath;
	private $outputDir;
	private $baseDir;

	public function __construct(view $archiveView = null){
		
		$this->buildPath = INSTEP_SYS_ASSETDIR . "archive_structure";
		$this->outputDir = INSTEP_SYS_ASSETDIR . "archive";
		$this->baseDir = INSTEP_SYS_ROOTDIR;
		
		$this->cleanup();
		
		if($archiveView == null) return;
		
		$this->setContent($archiveView);
	}
	
	
	private function getDependencies(){
		preg_match_all("/<(?:script|link)[^<>]+(?:src|href)=\"(?!http:\/\/)([^\"]+)[^>]+/i", $this->workingView, $dependencies);
		
		if( count($dependencies[1]) > 0 ){
			$this->dependencies = array_merge($this->dependencies, $dependencies[1]);
		}
	}
	
	private function copyDependencies(){
		// Copy Images
		$this->copyToDir($this->baseDir . "/presentation/images/*", $this->buildPath . "/presentation/images/");
		$this->copyToDir($this->baseDir . "/presentation/images/player/*", $this->buildPath . "/presentation/images/player/");
		
		foreach($this->dependencies as $dependency){
			$from = $this->baseDir . $dependency;
			$to = $this->buildPath . "/" . $dependency;
			
			//echo "copying {$from} to {$to}\n";
			
			copy($from, $to);
		}
		
		foreach($this->userDependencies as $dependency){
			$from = $dependency[0];
			$to = $this->buildPath . "/" . $dependency[1];
			copy($from, $to);
		}
	}
	
	private function writeHTML(){
		file_put_contents($this->buildPath . "/index.html", $this->workingView);
	}
	
	public function addDependency($from, $to){
		array_push($this->userDependencies, array($from, $to));
	}
	
	
	public function zip(){
		// Replace URL structures.
		$this->workingView->replaceAll(array(
			"base-url" => "",
			"include-url" => ""
		));
		
		// Get dependencies
		$this->getDependencies();	
	
	
		$this->copyDependencies();
		$this->writeHTML();
		
		$file = md5($this->workingView) . ".zip";
		
		$this->zipPath = "{$this->outputDir}/" . $file;

		shell_exec("cd {$this->buildPath}; zip -r ../archive/{$file} *");
	}
	
	public function getZipPath(){
		return $this->zipPath;
	}
	
	public function removeZip(){
		unlink($this->zipPath);
		$this->cleanup();
	}

	private function cleanup(){
		$mask = $this->buildPath . "/presentation/flash/*.flv";
		array_map("unlink", glob($mask));
	}
	
	public function setContent(view $content){
		$this->originalView = $content;
		$this->workingView = $content;	
	}


	private function copyToDir($pattern, $dir){
	    foreach (glob($pattern) as $file) {
	        if(!is_dir($file) && is_readable($file)) {
	            $dest = realpath($dir) . "/" . basename($file);
	            copy($file, $dest);
	        }
	    }    
	}

}

?>