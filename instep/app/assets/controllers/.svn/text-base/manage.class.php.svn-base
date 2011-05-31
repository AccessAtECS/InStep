<?php

class manage extends controller {

	private $m_user;
	private $m_noRender = false;

	protected function noRender(){
		// Determine if we are sending output to the browser with this request
		return $this->m_noRender;
	}

	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();

		$this->m_inStep = new InStep();
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );
		
		// Bind requests
		
		$this->bind('user', "logInOut" );
		$this->bind('act', 'processRequest');
	}


	protected function logInOut(){
		// The user should either be logged out if they are currently logged in, or be taken to the login page if they are not currently logged in.
		
		if($this->m_user->getUserObject() == NULL){
			// User is logged out. display login page.
			$this->setViewport( new view('userLogin') );
			
		} else {
			// User is logged in. Log them out.
			PermissionManager::Logout();
					
			// We dont want to send output, as we're going to be using header(); for redirection.
			$this->m_noRender = true;		
			
			// Redirect back to homepage.
			$this->redirect(INSTEP_BASEURL . "home/message/1");
		}
	}
	
	protected function processRequest(){
		// The user has sent us data.
		parse_str($_SERVER['QUERY_STRING']);
		
		if(User::Login($_POST['username'], $_POST['password'], $institution) ) {
			if(isset($_SESSION['redirectURI'])){
				$r = $_SESSION['redirectURI'];
				unset($_SESSION['redirectURI']);
				$this->redirect($r);
			} else {
				$this->redirect(INSTEP_BASEURL . "home");
			}
		} else {
			$this->redirect(INSTEP_BASEURL . "home/warning/2");
		}
	}

}

?>