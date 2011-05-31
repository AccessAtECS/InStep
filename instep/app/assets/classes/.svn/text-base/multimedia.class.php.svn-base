<?php

class multimedia {

	private $d_id = null;
	private $d_title;
	private $d_description;
	private $d_href;
	private $d_path;
	private $d_public;
	private $d_enabled;
	private $d_updated_by;
	private $d_updated_time;
	private $d_created_by;
	private $d_created_time;
	private $d_instep_asset;
	private $d_thumbnail;
	
	private $o_updated_by;
	private $o_created_by;
	
	private $o_infolio_asset = null;
	
	private $m_if_database;
	private $m_is_database;
	
	// InStep specific data
	public $s_record = false;
	private $s_level;
	private $s_name;
	private $s_date = false;
	
	private $s_owner;
	
	public function __construct($id = null){
		if($id == null) return;
		$this->d_id = $id;
		
		$is = new InStep();
		
		// Set up database connections
		$this->m_if_database = db::singleton();
		$this->m_is_database = $is->db();
		
		$p = $this->m_if_database->single("SELECT * FROM assets WHERE type='video' AND id =" . $this->m_if_database->real_escape_string($id));
		if(!empty($p)) {
			$this->d_title = $p[0]['title'];
			$this->d_description = $p[0]['description'];
			$this->d_href = $this->getNativeObject()->getFullHref(null, true);
			$this->d_path = $this->getNativeObject()->getFilePath();
			$this->d_public = $p[0]['public'];
			$this->d_enabled = $p[0]['enabled'];
			$this->d_updated_by = $p[0]['updated_by'];
			$this->d_updated_time = new DateTime($p[0]['updated_time'], new DateTimeZone('Europe/London'));
			$this->d_created_by = $p[0]['created_by'];
			$this->d_created_time = new DateTime($p[0]['created_time'], new DateTimeZone('Europe/London'));
			$this->d_instep_asset = $p[0]['instep_asset'];

			$this->d_thumbnail = INSTEP_SYS_REALBASEURL . "images/size_thumbnail/{$id}/";
			
			// Set up objects
			$this->o_updated_by = new User($this->d_updated_by);
			$this->o_created_by = new User($this->d_created_by);
			
			// Get the users name
			$this->o_updated_by->getFullName();
			
			// Pull out instep data
			$s = $this->m_is_database->single("SELECT * FROM asset WHERE id=" . $this->m_is_database->real_escape_string($id));
			if(!empty($s)){
				$this->s_record = true;
				$this->s_level = $s[0]['level'];
				$this->s_date = new DateTime($s[0]['date'], new DateTimeZone('Europe/London'));
				
				$this->d_title = $s[0]['name'];
			}
		} else {
			// Check to see if we have an instep asset, if so we need to remove it.
			throw new Exception("No video object with that ID!");
		}
	}
	
	public function commit(){
		$data['id'] = (int)$this->d_id;
		$data['name'] = $this->s_name;
		$data['level'] = $this->s_level;
		$data['owner'] = (int)$this->s_owner;
		$data['date'] = $this->s_date->format(DateTime::ATOM);

		$is = new InStep();
		$db = $is->db();
	
		// Notes, level.
		if($this->s_record){
			// Updating
			$db->update($data, "asset", array(array("", "id", $this->d_id)));
		} else {
			// New record
			$db->insert($data, "asset");
		}
		
		$db->runBatch();
		if($this->d_id == null) $this->d_id = $db->insert_id;	
		$this->s_record = true;
		return $this->d_id;
	}
	
	public function getId(){
		return $this->d_id;
	}
	
	public function getHref(){
		return $this->d_href;
	}
	
	public function getDescription(){
		return $this->d_description;
	}

	public function getTitle(){
		return $this->d_title;
	}
	
	public function getPublic(){
		return $this->d_public;
	}
	
	public function getEnabled(){
		return $this->d_enabled;
	}

	public function getUpdatedTime(){
		return $this->d_updated_time;
	}
	
	public function getUpdatedBy(){
		return $this->o_updated_by;
	}
	
	public function getCreatedTime(){
		return $this->d_created_time;
	}
	
	public function getCreatedBy(){
		return $this->o_created_by;
	}
	
	public function getNotes(){
		return $this->s_notes;
	}
	
	public function getLevel(){
		return $this->s_level;
	}
	
	public function getTags(){
		return $this->s_tags;
	}
	
	public function getThumbnail(){
		return $this->d_thumbnail;
	}
	
	public function getDate(){
		if($this->s_date){
			return $this->s_date;
		} else {
 			return $this->getCreatedTime();
		}
	}
	
	public function getIsInStep(){
		return (BOOL) $this->d_instep_asset;
	}
	
	public function getNativeObject(){
		if($this->o_infolio_asset == null){
			$this->o_infolio_asset = new Video($this->d_id);
		}
		
		return $this->o_infolio_asset;
	}
	
	public function getPath(){
		return $this->d_path;
	}

	public function setIsInStep($val){
		$instep_asset = (int) $val;
		$this->m_if_database->update(array("instep_asset" => $instep_asset), "assets", array(array("", "id", $this->d_id)))->run();
		return true;
	}
	
	public function setName($name){
		$this->s_name = $name;
	}
	
	public function setOwner(userWrapper $user){
		$this->s_owner = $user->getUserObject()->getId();
	}
	
	public function setId($id){
		$this->d_id = $id;
	}
	
	public function setLevel($level){
		$this->s_level = (int)$level;
	}
	
	public function setDate(DateTime $date){
		$this->s_date = $date;
	}
	
	public function addTag(instep_tag $tag){
		if(!$this->d_id) throw new Exception("Cannot add tag without being committed first!");

		$is = new InStep();
		$db = $is->db();		
		
		$check = $db->single("SELECT * FROM asset_tag WHERE tag_id = {$tag->getId()} AND asset_id = $this->d_id");
		
		if(!empty($check)) return;
		
		
		$data['asset_id'] = $this->d_id;
		$data['tag_id'] = $tag->getId();
		
		$db->insert($data, "asset_tag");
		$db->runBatch();
		return true;
	}
	
	public function removeTag(instep_tag $tag){
		if(!$this->d_id) throw new Exception("Cannot add tag without being committed first!");
	}
	
	public function removeAllTags(){
		$is = new InStep();
		$db = $is->db();		
		
		$db->single("DELETE FROM asset_tag WHERE asset_id = $this->d_id");
		return true;
	}

}

?>