<?php

class upload extends controller {

	private $m_user;
	private $m_inStep;

	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();
		
		$this->m_inStep = new InStep();
		
		
		$this->m_inStep->addAsset("scripts/upload.js", InStep::ASSET_JAVASCRIPT);
		
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );
		
		$this->bindDefault('addContent');
	}
	
	protected function addContent(){
		// Just render the prototype for now.
		$this->setViewport( new view("addContentPrototype") );
		
		$this->viewport()->replace('realbase', INSTEP_SYS_REALBASEURL);
	}

}

?>