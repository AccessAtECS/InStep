<?php

class view extends controller {

	private $m_user;
	private $m_inStep;

	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();
		
		$this->m_inStep = new inStepController();
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );	
		
		// Default route
		$this->bindDefault('displaySearch');
		
		// Any route past the controller is a search request
		$this->bind('[\w\d\+]+', 'displayResults');
	}

}

?>