<?php
// Class wraps up the InFolio User class so that we can use it in InStep
class userWrapper {

	private $m_UserObject = FALSE;
	
	private $m_userName = "";
	
	public function __construct(){
		// Require the InFolio user class.
		require_once('model/User.class.php');
	}
	
	public function getUser(){
		if( isset($_SESSION) ) {
			$this->m_UserObject = User::RetrieveBySessionData($_SESSION);
			if($this->m_UserObject != NULL) $this->queryInFolio();
		}	
	}
	
	private function queryInFolio(){
		$this->m_userName = $this->m_UserObject->getFirstName() . " " . $this->m_UserObject->getLastName();
	}
	
	public function getUserObject(){
		$this->getUser();
		return $this->m_UserObject;
	}
	
	public function getName(){
		return $this->m_userName;
	}

}

?>