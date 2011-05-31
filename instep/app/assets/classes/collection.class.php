<?php

class collection {

	private $m_sort;
	private $m_limit;
	private $m_query = array();
	private $m_collection = array();
	private $m_type;
	
	private $m_target_id;

	const SORT_DESC = 1;
	const SORT_ASC = 2;
	
	const LINK_INSTEP = 3;
	const LINK_INFOLIO = 4;
	
	const TYPE_ASSET = 5;
	const TYPE_NOTE = 6;
	const TYPE_TAG = 7;
	const TYPE_COMPARISON = 8;
	const TYPE_INSTEPASSET = 9;
	const TYPE_TAGNAME = 10;


	public function __construct($type = collection::TYPE_ASSET){
		$type = (int)$type;
		if(is_int($type) == false) throw new Exception("Constructor argument type must be a valid type");
		if($type >= collection::TYPE_ASSET && $type <= collection::TYPE_TAGNAME){
			$this->m_type = $type;
		} else {
			throw new Exception("The type specified is invalid");
		}
	}


	public function setQuery(array $arg){
		//array("", "fID", "=", $id)
		array_push($this->m_query, $arg);
	}

	public function setSort($fieldName, $sortID){
		$sortID = (int)$sortID;
		if($sortID != collection::SORT_DESC && $sortID != collection::SORT_ASC) throw new Exception("Sort ID invalid.");
		
		switch($sortID){
		
			case collection::SORT_DESC:
				$this->m_sort = " ORDER BY " . $fieldName . " DESC";
			break;
			
			case collection::SORT_ASC:
				$this->m_sort = " ORDER BY " . $fieldName . " ASC";
			break;
		
		}
		
	}
	
	public function setTargetId($id){
		$this->m_target_id = $id;
	}
	
	public function setLimit($first, $last = ""){
		$this->m_limit = ($last == "") ? " LIMIT $first" : "LIMIT $first, $last";
	}	

	public function get(){
	
		switch($this->m_type){
		
			case collection::TYPE_ASSET:
				
				
				// Pull assets from InFolio system using their classes.
				
				$db = db::singleton();
						
				$target_user = new User($this->m_target_id);
				$assets = Asset::RetrieveUsersAssets($target_user, "(" . ltrim($db->parseConditions($this->m_query), " AND") . ")" );

				if(empty($assets)) throw new Exception("No results returned");
				
				foreach($assets as $vid){
					array_push($this->m_collection, new multimedia((int)$vid->getId()));
				}				
				
				/*
				$this->m_query = array_merge(array(array("", "instep_asset", "=", 1)), $this->m_query);

				$db = db::singleton();
				
				$output = $db->select(array("id"), "assets", $this->m_query, $this->m_sort . $this->m_limit)->run();
				if(empty($output)) throw new Exception("No results returned");
				
				foreach($output[0] as $vid){
					array_push($this->m_collection, new multimedia((int)$vid['id']));
				}
				*/
			
			break;
			
			case collection::TYPE_NOTE:
				$is = new InStep();
				$db = $is->db();
			
				$output = $db->select(array("id"), "notes", $this->m_query, $this->m_sort . $this->m_limit)->run();
				if(empty($output)) throw new Exception("No results returned");
				
				foreach($output[0] as $note){
					array_push($this->m_collection, new note((int)$note['id']));
				}			
			
			break;
			
			case collection::TYPE_TAG:
				$is = new InStep();
				$db = $is->db();
			
				$output = $db->select(array("tag_id"), "asset_tag", $this->m_query, $this->m_sort . $this->m_limit)->run();
				if(empty($output)) throw new Exception("No results returned");
				
				foreach($output[0] as $tag){
					array_push($this->m_collection, new instep_tag((int)$tag['tag_id']));
				}				
			break;
			
			case collection::TYPE_TAGNAME:
				$is = new InStep();
				$db = $is->db();
			
				$output = $db->select(array("id"), "tag", $this->m_query, $this->m_sort . $this->m_limit)->run();
				if(empty($output)) throw new Exception("No results returned");
				
				foreach($output[0] as $tag){
					array_push($this->m_collection, new instep_tag((int)$tag['id']));
				}				
			break;
			
			case collection::TYPE_INSTEPASSET:
				$is = new InStep();
				$db = $is->db();
			
				$output = $db->select(array("id"), "asset", $this->m_query, $this->m_sort . $this->m_limit)->run();
				if(empty($output)) throw new Exception("No results returned");
				
				foreach($output[0] as $asset){
					array_push($this->m_collection, new multimedia((int)$asset['id']));
				}
			break;
			
			case collection::TYPE_COMPARISON:
				$is = new InStep();
				$db = $is->db();
			
				$output = $db->select(array("id"), "comparison", $this->m_query, $this->m_sort . $this->m_limit)->run();
				if(empty($output)) throw new Exception("No results returned");
				
				foreach($output[0] as $asset){
					array_push($this->m_collection, new comparison((int)$asset['id']));
				}			
			break;
		
		}
	

		
		return $this->m_collection;
	}
	
	public function __toString(){
		return $this->get();
	}

}

?>