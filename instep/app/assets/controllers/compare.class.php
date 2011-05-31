<?php

class compare extends controller {

	private $m_user;
	private $m_inStep;
	private $m_noRender = false;
	
	private $s_assets = array();

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
		
		// Controls comparing two items & adding a review
		$this->bind('([0-9]+-[0-9]+)(/(?P<user_id>[0-9]+))?(?P<archive>/archive)?$', 'renderComparison');
		$this->bind('([0-9]+-[0-9]+)(/(?P<user_id>[0-9]+))?/add$', 'addReview');
		
		$this->bind('view$', 'comparisonSearch');
		
		// View a comparison
		$this->bind('view/(?P<id>[0-9]+)(?P<archive>/archive)?$', 'viewComparison');
		
		// Default renderer
		$this->bindDefault('compareSelector');
	}
	
	protected function compareSelector() {
		$this->m_inStep->addAsset("scripts/upload.js", InStep::ASSET_JAVASCRIPT);
		$this->m_inStep->addAsset("scripts/search.js", InStep::ASSET_JAVASCRIPT);

		if(isset($_GET['learner'], $_GET['activity'], $_GET['date'])){
			$this->displayResults();
		} else {		
			$this->setViewport( new view("searchComparison") );
			
			$thisUser = $this->m_user->getUserObject();
			
			// Get a list of users.
			$u = new User();
			$users = $u->RetrieveUsers($thisUser, $thisUser->getInstitution());

			
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
			
			$this->viewport()->replace("userList", $output);
			
		}
		
		$this->setup();
		
	}

	protected function displayResults(){
    	
    	$this->setViewport( new view("searchResults") );
    	
    	// Only show results that have a record in instep database!
    	
    	$learner = (int)$_GET['learner']; 
    	$learnerObj = new User($learner);
    	
    	$activity = $_GET['activity'];
		$date = new DateTime($_GET['date'], new DateTimeZone('Europe/London'));	
		
		try {
		
            $collection = new collection();
            
            $collection->setTargetId($learner);
            
            
			$collection->setQuery(array("AND", "instep_asset", "=", 1));

			
			$this->viewport()->replace("learner", $learnerObj->getFullName());

			
			$videos = $collection->get();
	
			
			$frag = new view("frag.result");
			$output = new view();
			
			foreach($videos as $video){
				if($video->s_record){
				
					if(!empty($activity)){
						if(strpos(strtolower( $video->getTitle() ), strtolower($activity) ) === false) continue;
					}
					
					if(!empty($_GET['date'])) if($video->getDate()->diff($date)->days > 30) continue;
				
					$frag->replaceAll(array(
						"id" => $video->getId(),
						"User" => $video->getCreatedBy()->getFullName(),
						"Title" => $video->getTitle(),
						"Date" => $video->getDate()->format('d/m/Y'),
						"Description" => $video->getDescription(),
						"thumbnail" => "<img src='{$video->getThumbnail()}' alt='{$video->getTitle()}' class='thumb' />"
					));					
					
					$output->append($frag);
					$frag->reset();
				}
			}
		
			$this->viewport()->replace("results", $output);
		
		} catch(Exception $e){
			$this->viewport()->set($e->getMessage());
		}
		
		$this->setup();
	}
	
	protected function renderComparison($args) {
		$this->m_inStep->addAsset("scripts/comparison.js", InStep::ASSET_JAVASCRIPT);
		$this->m_inStep->addAsset("scripts/swfobject.js", InStep::ASSET_JAVASCRIPT);
		
		$ids = $this->context();
		$ids = explode("-", $ids[0]);

		$levels = json_decode(file_get_contents(INSTEP_SYS_ROOTDIR . "presentation/scripts/domainMap.json"), true);

		// Get the level bar fragment.
		$compareBar = new view("frag.compareBar");
		
		$this->setViewport(new view("compare"));
	
		if(isset($args['user_id'])){
			$u = new User($args['user_id']);
			$this->viewport()->replace("learner", " from learner " . $u->getFullName());
			$this->viewport()->replace("learnerID", $u->getId());
		} else {
			$this->viewport()->replace("learner", "");
			$this->viewport()->replace("learnerID", "");
		}
	
		$assetPaths = array();
	
		$i = 1;
		foreach($ids as $id) {
			$id = (int)$id;
			$review = review::renderForm($id, new view("compareForm"));
			$forms[$i] = $review;
			$asset = new multimedia($id);
			
			array_push($assetPaths, array("href" => $asset->getHref(), "path" => $asset->getPath()));
			
			$savedLevel = $asset->getLevel();

			$domain = ceil($savedLevel / 3);
			$level = $savedLevel % 3;
			if($level == 0) $level = 3;

			$compareBar = $this->generateComparisonBars($compareBar, $savedLevel, $level, $domain);

			$review->replace('displayLevel', $compareBar);
			$this->viewport()->replace("form" . $i, $review);
			
			$i++;
			$compareBar->reset();
		}
				
		$this->setup();

		if(isset($args['archive'])){
			$this->createArchive($assetPaths);
		}
		
	}
	
	private function createArchive($assetPaths){
		// Create the output view.
		$o = new view();
		$o->set($this->superview());
		
		// Set the content.
		$o->replace('viewport', $this->viewport());
		
		$archive = new archive();
		
		$archive->addDependency(INSTEP_SYS_ROOTDIR . "presentation/flash/player.swf", "presentation/flash/player.swf");
		$o->replace("/presentation/flash/player.swf", "presentation/flash/player.swf", view::REPLACE_DIRECT);
		
		$o->replace("/<!--NONARCHIVE-->.*?<!--\/NONARCHIVE-->/is", "", view::REPLACE_REGEX);
		
		foreach($assetPaths as $asset){
			$new_a = "presentation/flash/" . basename($asset['path']);

			$o->replace($asset['href'], $new_a, view::REPLACE_DIRECT);
			$archive->addDependency($asset['path'], $new_a);
		}

		$o->replace("file','presentation/flash/", "file','", view::REPLACE_DIRECT);
		
		$archive->setContent($o);
		
		$archive->zip();
		
		$path = $archive->getZipPath();
		
		if (file_exists($path)) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($path));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($path));
		    ob_clean();
		    flush();
		    readfile($path);
		    
		    $archive->removeZip();
		    
		    exit;
		} else {
			echo "Unable to read file.";
			exit;
		}
	}
	
	protected function addReview(){
		$this->m_noRender = true;
		
		if(isset($_POST['notes'], $_POST['review1'], $_POST['review2'])){
			if($_POST['notes'] == "") InStep::sendResponse(5077, "Please fill in notes before submitting a comparison.");

			$comparison = new comparison();
			
			// Get the learner information
			if(!empty($_POST['learnerID'])){
				$u = new User((int)$_POST['learnerID']);
				$comparison->setLearner($u);
			}
			
			$comparison->setIdFrom($_POST['review1']);
			$comparison->setIdTo($_POST['review2']);
			$comparison->setNotes($_POST['notes']);
			$comparison->setOwner($this->m_user->getUserObject());
		
			$id = $comparison->commit();
			
			InStep::sendResponse(200, "Review Added", array("comparison_id" => $id));
		} else {
			InStep::sendResponse(5077, "Please fill in notes before submitting a comparison.");
		}
		
		$this->setup();
	}
	
	protected function comparisonSearch(){
		$this->m_inStep->addAsset("scripts/comparisonList.js", InStep::ASSET_JAVASCRIPT);		
		$this->setViewport(new view("comparisonList"));
		
		try {
			$collection = new collection(collection::TYPE_COMPARISON);
			
			$collection->setQuery( array("", "owner", "=", $this->m_user->getUserObject()->getId()) );
	
			
			$comparisons = $collection->get();
	
			$frag = new view("frag.result");
			$output = new view();
			
			foreach($comparisons as $comparison){
				$user = $comparison->getFrom()->getCreatedBy();
				$userName = $user->getFirstName() . " " . $user->getLastName();
			
				$frag->replaceAll(array(
					"id" => $comparison->getId(),
					"User" => $userName,
					"Title" => "",
					"Date" => $comparison->getDate()->format('d/m/Y'),
					"Description" => InStep::shorten($comparison->getNotes(), 40),
					"thumbnail" => "<img src='{$comparison->getFrom()->getThumbnail()}' alt='{$comparison->getFrom()->getTitle()}' width='100' height='100' /> <img src='{$comparison->getTo()->getThumbnail()}' alt='{$comparison->getTo()->getTitle()}' width='100' height='100' />"
				));					
				
				$output->append($frag);
				$frag->reset();
			}
		
			$this->viewport()->append($output);
		
		} catch(Exception $e){
			$this->viewport()->set($e->getMessage());			
		}
		
		$this->setup();
	}
	
	protected function viewComparison($args){
		$this->m_inStep->addAsset("scripts/swfobject.js", InStep::ASSET_JAVASCRIPT);
		$this->m_inStep->addAsset("scripts/comparison.js", InStep::ASSET_JAVASCRIPT);

		$context = $this->context();
		$this->setViewport(new view("viewComparison"));
		
		
		try {
			$comparison = new comparison((int) $args['id']);	
			
			
			if($comparison->getLearner() != null){
				$this->viewport()->replace("learner", " from learner " . $comparison->getLearner()->getFullName());
			} else {
				$this->viewport()->replace("learner", "");
			}
			
			$compare = new view("viewCompare");

			$levels = json_decode(file_get_contents(INSTEP_SYS_ROOTDIR . "presentation/scripts/domainMap.json"), true);
			
			$reviews = array(
				review::renderForm($comparison->getIdFrom(), new view("compareForm")),	
				review::renderForm($comparison->getIdTo(), new view("compareForm"))		
			);

			$compareBar = new view("frag.compareBar");

			$assetPaths = array(
				array("href" => $comparison->getFrom()->getHref(), "path" => $comparison->getFrom()->getPath()),
				array("href" => $comparison->getTo()->getHref(), "path" => $comparison->getTo()->getPath())
			);

			for($i=1; $i<=2;$i++){	
				// Get the level bar fragment.
	
				$savedLevel = ($i == 1) ? $comparison->getFrom()->getLevel() : $comparison->getTo()->getLevel();
	
				$domain = ceil($savedLevel / 3);
				$level = $savedLevel % 3;
				if($level == 0) $level = 3;

				$compareBar = $this->generateComparisonBars($compareBar, $savedLevel, $level, $domain);
	
	
				// Set up form.
				$reviews[$i-1]->replace('displayLevel', $compareBar);
				$compare->replace("form" . $i, $reviews[$i-1]);
				
				$compareBar->reset();
			}
			
			$compare->replace("notes", $comparison->getNotes());
			
			$this->viewport()->replace("comparison", $compare);
		} catch(Exception $e){
			$this->viewport()->replace("comparison", $e->getMessage());
		}
		
		$this->setup();
		
		if(isset($args['archive'])){
			$this->createArchive($assetPaths);
		}
	}
	
	private function generateComparisonBars(view $compareBar, $savedLevel, $level, $domain){
		$levels = json_decode(file_get_contents(INSTEP_SYS_ROOTDIR . "presentation/scripts/domainMap.json"), true);
		
		$percentage = round(($level / 3)*100);
		
		$compareBar->replace('overallProgress', floor(500 * ($savedLevel / 18)) + 36 );
		$compareBar->replace('levelNum', $level);
		$compareBar->replace('category', $levels[$domain]['name']);
		$compareBar->replace('progress', $percentage);	
		$compareBar->replace('level', $levels[$domain]['levels'][$level-1]['name']);
		$compareBar->replace('description', $levels[$domain]['description']);
		
		return $compareBar;		
	}
	
	protected function noRender() {
		return $this->m_noRender;
	}
	
	private function setup(){
		$this->m_inStep->setupSuperview( $this->superview(), __CLASS__, $this->m_user );
	}
}

?>