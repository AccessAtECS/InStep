<?php

class review extends controller {

	private $m_user;
	private $m_inStep;
	private $m_noRender = false;
	private $m_thisAsset;

	public function renderViewport(){
		// We want access to the user object.
		$this->m_user = $this->objects("userWrapper");
		$this->m_user->getUser();
		
		if($this->m_user->getUserObject() == NULL) {
			$_SESSION['redirectURI'] = $_SERVER['REQUEST_URI'];
			$this->redirect(INSTEP_BASEURL . "manage/user");
			return;
		}
				
		$this->m_inStep = new InStep();
		
		
		$this->m_inStep->addAsset("scripts/upload.js", InStep::ASSET_JAVASCRIPT);
		$this->m_inStep->addAsset("scripts/swfobject.js", InStep::ASSET_JAVASCRIPT);
		$this->m_inStep->addAsset("scripts/review.js", InStep::ASSET_JAVASCRIPT);
		
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );
		
		$this->bind("([0-9]+)(/(?P<user_id>[0-9]+))?$", "reviewAsset");
		$this->bind("([0-9]+)(/(?P<user_id>[0-9]+))?/edit$", "edit");
		$this->bind("([0-9]+)(/(?P<user_id>[0-9]+))?/tags$", "getTags");
		$this->bind("tags$", "getAllTags");
		$this->bind("note/update", "updateNote");
		$this->bindDefault('selector');
	}
	
	protected function selector(){
		if(isset($_GET['learner'], $_GET['activity'], $_GET['date'])){
			$this->displayResults();
		} else {
		
			$this->setViewport( new view("searchReview") );
			
			$thisUser = $this->m_user->getUserObject();
			
			// Get a list of users.
			$u = new User();
			$users = $u->RetrieveUsers($thisUser, $thisUser->getInstitution());
			
			if(count($users) > 0){
				
				$option = new view("frag.option");
				$output = new view();
				
				foreach($users as $user){
					$option->replaceAll(array(
						"val" => $user->getId(),
						"text" => $user->getFullName()
					));
					
					$output->append($option);
					$option->reset();
				}
			} else {
				$output = new view();
			}
			
			$this->viewport()->replace("userList", $output);
			
		}	
	}
	
	protected function displayResults(){
    	
    	$this->setViewport( new view("reviewResults") );
    	
    	$learner = (int)$_GET['learner']; 
    	$learnerObj = new User($learner);
    	
    	
    	$activity = $_GET['activity'];
		$date = new DateTime($_GET['date'], new DateTimeZone('Europe/London'));	
		$onlyNew = isset($_GET['newContent']) ? true : false;
		
		try {
		
			$collection = new collection();
			
			$collection->setTargetId($learner);
			
			$collection->setQuery(array("AND", "instep_asset", "=", 1));
						
			
			
			
			$videos = $collection->get();
	
			
			$this->viewport()->replace("learner", $learnerObj->getFullName());
	
			
			$frag = new view("frag.result");
			$output = new view();
			
			foreach($videos as $video){
			
				if($video->s_record){
					// If we're only selecting new records we don't want to display this.
					if($onlyNew) continue;
					$new = "";
				} else {
					$new = " <span class='new'>(New!)</span>";
				}
				
				// Check to see 
				if(!empty($activity)) if(stripos($video->getTitle(), $activity) === false && stripos($video->getDescription(), $activity) === false) continue;
				
				if(!empty($_GET['date'])) if($video->getDate()->diff($date)->days > 30) continue;

			
				$frag->replaceAll(array(
					"id" => $video->getId(),
					"User" => $video->getCreatedBy()->getFullName(),
					"Title" => $video->getTitle() . $new,
					"Date" => $video->getDate()->format('d/m/Y'),
					"Description" => InStep::shorten($video->getDescription(), 40),
					"thumbnail" => "<img src='{$video->getThumbnail()}' alt='{$video->getTitle()}' />"
				));
				
				$output->append($frag);
				$frag->reset();
			}
		
			$this->viewport()->replace("results", $output);
		
		} catch(Exception $e){
			$this->viewport()->set($e->getMessage());
		}
		
	}

	protected function getTags(){
		$this->m_noRender = true;
		$context = $this->context();
		
		$id = $context[0];
		
		$this->m_thisAsset = new multimedia($id);
		
		$collection = new collection(collection::TYPE_TAG);
		$collection->setQuery(array("", "asset_id", "=", $this->m_thisAsset->getId()));
		$tags = $collection->get();
		
		$output = array();
		foreach($tags as $tag){
			array_push($output, $tag->getName());
		}
		
		InStep::sendResponse(200, "Tags fetched", array("tags" => $output));
	}
	
	protected function getAllTags(){
		$this->m_noRender = true;
		
		$collection = new collection(collection::TYPE_TAGNAME);
		if(!empty($_GET['text'])) $collection->setQuery(array("", "name", "LIKE", "%" . $_GET['text'] . "%"));
		$tags = $collection->get();
		
		$output = array();
		foreach($tags as $tag){
			array_push($output, $tag->getName());
		}
		
		InStep::sendResponse(200, "Tags fetched", array("tags" => $output));	
	}

	protected function edit() {
		$this->m_noRender = true;
		if(empty($_POST)) {
			// response
			return;
		} 
		
		$domainLevel = (int)$_POST['domainLevel'];
		$level = (int)$_POST['level'];
		
		$resultLevel = ($domainLevel - 1 ) * 3;
		$resultLevel = $resultLevel + $level;
		
		try {
			$asset = new multimedia( isset($_POST['review-asset_id']) ? $_POST['review-asset_id'] : '');
			$asset->setName($_POST['activity']);
			$asset->setId($_POST['review-asset_id']);
			$asset->setDate( new DateTime($_POST['review-date'], new DateTimeZone('Europe/London') ) );
			$asset->setLevel( $resultLevel );
			$asset->setOwner($this->m_user);
			
			$asset->commit();
		} catch(Exception $e){
			InStep::sendResponse(509, $e->getMessage());
		}
		
		if(isset($_POST['tags'])){
		
			foreach($_POST['tags'] as $k => $t){
				try {
					$tag = new instep_tag($k, instep_tag::TYPE_NAME);
				} catch(Exception $e){
					$tag = new instep_tag();
					$tag->setName($k);
					$tag->commit();
				}
				$asset->addTag($tag);			
			}
		}
		
		if($_POST['review-notes'] != ""){
			$note = new note();
			$note->setNote($_POST['review-notes']);
			$note->setAssetIdFromAsset($asset);
			$note->setOwner( $this->m_user->getUserObject() );
			$note->commit();
		}
		
		
		InStep::sendResponse(200, "Update successful");
	}
	
	protected function reviewAsset($args){
		// Show the asset and allow the user to review
		$asset_id = $this->context();
		
		$this->setViewport(new view());
		
		if(isset($_GET['u'])){
			$messageView 	= new view("message");
			$messageView->replace("message", "Content updated - Your review has now been added.");
			$messageView->replace("type", "message");
			$this->viewport()->append( $messageView );
		}
		
		$this->viewport()->append(new view("review"));
		
		if(isset($args['user_id'])){
			$u = new User($args['user_id']);
			$this->viewport()->replace("learner", " from learner " . $u->getFullName());
		} else {
			$this->viewport()->replace("learner", "");
		}
		
		$reviewForm = $this->renderForm($asset_id[0], new view("reviewForm"));
		$this->viewPort()->replace("review-form", $reviewForm);
		if($this->m_thisAsset->s_record){
			$this->viewport()->replace("type", "");
		} else {
			$this->viewport()->replace("type", "New");
		}

	}
	
	public function renderForm($asset_id, view $reviewForm) {

		$user = $this->objects("userWrapper");
		$user->getUser();

		$this->m_thisAsset = new multimedia($asset_id);

		$reviewForm->replace("title", $this->m_thisAsset->getTitle());
		$reviewForm->replace("reviewFormId", $asset_id);
		$reviewForm->replace("date", $this->m_thisAsset->getDate()->format('d/m/Y'));
		$reviewForm->replace("href", $this->m_thisAsset->getHref());
		$reviewForm->replace("asset_id", $this->m_thisAsset->getId());
		$reviewForm->replace("activity", $this->m_thisAsset->getTitle());
		$reviewForm->replace("learner", $this->m_thisAsset->getUpdatedBy()->getFullName());
		$reviewForm->replace("thumbnail", $this->m_thisAsset->getThumbnail());
		
		$levels = json_decode(file_get_contents(INSTEP_SYS_ROOTDIR . "presentation/scripts/domainMap.json"), true);
		
		// Get the level
		$savedLevel = $this->m_thisAsset->getLevel();
		if($savedLevel != null){
			$domain = ceil($savedLevel / 3);
			
			$levelX = $savedLevel % 3;
	
			switch((int)$domain){
			
				case 1:
	
					$reviewForm->replaceAll(array(
						"selectRespond" => "",
						"selectEngage" => "",
						"selectCando" => "",
						"selectImprove" => "",
						"selectSkilled"	 => "",
						"domainLevel" => "1"						
					));
					
				break;
				
				case 2:
	
					$reviewForm->replaceAll(array(
						"selectRespond" => " selected",
						"selectEngage" => "",
						"selectCando" => "",
						"selectImprove" => "",
						"selectSkilled"	 => "",
						"domainLevel" => "2"							
					));
					
				break;
				
				case 3:
					$reviewForm->replaceAll(array(
						"selectRespond" => " selected",
						"selectEngage" => " selected",
						"selectCando" => "",
						"selectImprove" => "",
						"selectSkilled"	 => "",
						"domainLevel" => "3"						
					));			
				break;
				
				case 4:
					$reviewForm->replaceAll(array(
						"selectRespond" => " selected",
						"selectEngage" => " selected",
						"selectCando" => " selected",
						"selectImprove" => "",
						"selectSkilled"	 => "",
						"domainLevel" => "4"							
					));			
				break;
				
				case 5:
					$reviewForm->replaceAll(array(
						"selectRespond" => " selected",
						"selectEngage" => " selected",
						"selectCando" => " selected",
						"selectImprove" => " selected",
						"selectSkilled"	 => "",
						"domainLevel" => "5"							
					));			
				break;
				
				case 6:
					$reviewForm->replaceAll(array(
						"selectRespond" => " selected",
						"selectEngage" => " selected",
						"selectCando" => " selected",
						"selectImprove" => " selected",
						"selectSkilled"	 => " selected",
						"domainLevel" => "6"						
					));			
				break;
			
			}
	
			if($levelX == 1){
				$reviewForm->replaceAll(array(
					"levelBar2" => "",
					"levelBar3" => "",
					"barLevel" => "1"
				));
				$levelIndex = 0;
			}
			if($levelX == 2){
				$reviewForm->replaceAll(array(
					"levelBar2" => "selected",
					"levelBar3" => "",
					"barLevel" => "2"
				));
				$levelIndex = 1;
			}
			if($levelX == 0){
				$reviewForm->replaceAll(array(
					"levelBar2" => "selected",
					"levelBar3" => "selected",
					"barLevel" => "3"
				));
				$levelIndex = 2;
			}
			

			$reviewForm->replace("domainName", $levels[(int)$domain]['name'] . " - level " . ((int)$levelIndex+1));
			$reviewForm->replace("levelName", $levels[(int)$domain]['levels'][$levelIndex]['name']);
			
		} else {
			$reviewForm->replaceAll(array(
				"levelBar2" => "",
				"levelBar3" => "",
				"barLevel" => "1"
			));	
			
			$reviewForm->replaceAll(array(
				"selectRespond" => "",
				"selectEngage" => "",
				"selectCando" => "",
				"selectImprove" => "",
				"selectSkilled"	 => "",
				"domainLevel" => "1"						
			));	
			
			$reviewForm->replace("domainName", $levels[1]['name'] . " - level 1");
			$reviewForm->replace("levelName", $levels[1]['levels'][0]['name']);						
		}
		
		
		try {
			// Get notes
			$collection = new collection(collection::TYPE_NOTE);
			$collection->setQuery(array("", "asset_id", "=", $this->m_thisAsset->getId()));
			$notes = $collection->get();
			
			$template = new view("frag.note");
			$output = new view();
			
			foreach($notes as $note){
				$owner = $user->getUserObject()->getId() == $note->getOwner()->getId() ? true : false;
				$template->replace("id", $note->getId());
				$template->replace("note", $note->getNote());
				$template->replace("user", $note->getOwner()->getFirstName() . " " . $note->getOwner()->getLastName());
				$template->replace("time", $note->getTime()->format("d/m/Y"));
				if($owner){
					$template->replace("edit", "<a href='#'>[Edit]</a>");
				} else {
					$template->replace("edit", "");				
				}
				$output->append($template);
				$template->reset();
			}
		} catch(Exception $e){
			$output = "";
		}
		
		$reviewForm->replace("notes", $output);

		return $reviewForm;
	}
	
	protected function updateNote(){
		if(!isset($_POST['id'], $_POST['note'])) InStep::sendResponse(500, "Required information not provided.");
		
		try {
			$note = new note((int)$_POST['id']);
			if($note->getOwner()->getId() == $this->m_user->getUserObject()->getId()){
				$note->setNote($_POST['note']);
				$note->commit();
				InStep::sendResponse(200, "Success");
			} else {
				InStep::sendResponse(607, "Inadequate permissions");
			}
		} catch(Exception $e){
			InStep::sendResponse(500, $e->getMessage());
		}
	}
	
	protected function noRender() {
		return $this->m_noRender;
	}

}

?>