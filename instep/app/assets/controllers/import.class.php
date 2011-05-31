<?php

class import extends controller {

	private $m_user;

	protected function noRender(){
		// Determine if we are sending output to the browser with this request
		return true;
	}

	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();

		$this->m_inStep = new InStep();
		
		// Bind requests
		$this->bindDefault("importData");
	}

	protected function importData(){
		if($this->m_user->getUserObject() == NULL) InStep::sendResponse(500, "You must be logged in to perform this action");
		
		if(isset($_GET['id'])){
		
			try {
				$asset = new multimedia((int) $_GET['id']);
		
				if(isset($_GET['isAsset'])){
					// Setting
					$asset->setIsInStep($_GET['isAsset']);
					InStep::sendResponse(200, "Asset Updated");
				} else {
					// Getting
					$isAsset = $asset->getIsInStep();
					InStep::sendResponse(200, "Asset Fetched", array("isAsset" => $isAsset));
				}

			} catch (Exception $e){
				InStep::sendResponse(500, $e->getMessage());
			}
		
		} else {
			InStep::sendResponse(500, "ID not set");
		}
	}

}

?>