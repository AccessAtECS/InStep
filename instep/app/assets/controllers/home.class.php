<?php

class home extends controller {

	protected $m_defaultHandler = "homepageHandler";
	
	private $m_user;
	private $m_inStep;
	
	// Messages used
	private $m_messages = array(
		"1" => "You have been successfully logged out.",
		"2" => "Incorrect username and password."
	);
	
	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();
		
		$this->m_inStep = new InStep();
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );
	
		// Set the default request handler
		$this->bindDefault($this->m_defaultHandler);		
		
		
		// Bind request patterns. Set these in order of precedence
		$this->bind('[message|warning]/\d+', "displayMessage" );
		// Debug output
		$this->bind('debug', "printDebugInformation" );
	}
	
	
	
	///////////////////////////////////////
	/*									 //
		Content generation functions	 //
	*/									 //
	///////////////////////////////////////
	
	
	protected function displayMessage(){
		// Set the views
		$homepage 		= new view("homepage");
		$messageView 	= new view("message");
		
		// Replace the message placeholder with the message
		$messageView->replace("message", 
			$this->m_messages[ end( $this->context() ) ]
		);
		
		$messageType = array_slice($this->context(), -2, 1);
		$messageView->replace("type", $messageType[0]);
		
		// Collate the views
		$messageView->append($homepage);
		
		/// Set the viewport
		$this->setViewport( $messageView );
	}
	
	protected function printDebugInformation(){
		// Render default homepage content
		$this->homepageHandler();
		
		// Render debug information
		$this->viewport()->append(
			"<p>" . var_export($this->objects(), true) . "</p>"
		);
		
	}
	
	protected function homepageHandler(){
		// Set the viewport to the homepage.
		
		// Is the user logged in?
		$name = $this->m_user->getName();
		
		if($name == ""){
			$this->setViewport( new view("homepage") );
		} else {
		
			$this->setViewport( new view("userHomepage") );
			
			$this->viewport()->replace('userName', $name);
		}
		
	}

}

?>