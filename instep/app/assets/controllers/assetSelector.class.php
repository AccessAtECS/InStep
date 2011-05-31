<?php

class assetSelector extends controller {

	private $m_user;
	private $m_inStep;

	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();
		
		$this->m_inStep = new InStep();
		
		
		//$this->m_inStep->addAsset("scripts/swfobject.js", InStep::ASSET_JAVASCRIPT);
		
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );	
		
		// Default route
		//$this->bindDefault('displaySearch');
		
		// Any route past the controller is a search request
		$this->bindDefault('renderPrototype');
	}
	
	protected function renderPrototype(){
		// Just render the prototype for now.
		$this->setViewport( new view("assetSelectorPrototype") );
		echo "<pre>";
		$user_id = $this->m_user->getUserObject()->getId();
		$institution_id = $this->m_user->getUserObject()->getInstitution()->getId();
		
		$db = db::singleton();
		$assets = $db->single("SELECT id, href, type, title FROM assets WHERE enabled = 1 AND created_by = $user_id");
		
		$listItem = new view();
		$listItem->set("<li><a href='review/{id}'>{text}</a></li>");
		
		$output = "";
		
		foreach($assets as $asset) {
			$listItem->replace("text", $asset['id'] . ": " . $asset['title']);
			$output .= $listItem->replace("id", $asset['id']);
			$listItem->reset();
		}
		
		$this->viewPort()->replace("list-items", $output);
		
		//print_r($assets);
		
		$userObject = $this->m_user->getUserObject()->getInstitution()->getId();
		//print_r($userObject);
		
		echo "</pre>";
		
		$this->viewport()->replace('realbase', INSTEP_SYS_REALBASEURL);
	}

}

?>