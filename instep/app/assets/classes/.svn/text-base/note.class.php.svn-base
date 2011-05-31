<?php

class note {

	private $id = null;
	private $note;
	private $time = null;
	private $asset_id;
	private $owner;

	private $asset;

	private $m_db;
	private $o_user = null;


	function __construct($id = null) {
		if($id == null) return;

		$is = new InStep();
		$this->m_db = $is->db();


		if(is_int($id) == false) throw new Exception("Note ID is not an integer");
		$this->id = $id;
		$note = $this->m_db->single("SELECT * FROM notes WHERE id = $this->id");
		if(empty($note)) throw new Exception("No note with that ID.", 404);
		$this->note = $note[0]['note'];
		$this->time = new DateTime($note[0]['time'], new DateTimeZone('Europe/London'));
		$this->asset_id = $note[0]['asset_id'];
		$this->owner = $note[0]['owner'];
		
	}
	
	public function commit(){

		$is = new InStep();
		$db = $is->db();
	
		$data['note'] = $this->note;
		if($this->time != null) $data['time'] = $this->time->format('Y-m-d H:i:s');
		$data['asset_id'] = (int)$this->asset_id;
		$data['owner'] = $this->owner;

		if($this->id == null) {
			$s = $db->insert($data, "notes");
		} else {
			$db->update($data, "notes", array(array("", "id", $this->id)));
		}
		$db->runBatch();
		if($this->id == null) $this->id = $db->insert_id;	
		
		return $this->id;
	}
	
	public function getId(){
		return $this->id;
	}


	public function getNote(){
		return $this->note;
	}
	
	public function getTime(){
		return $this->time;
	}
	
	public function getAssetId(){
		return $this->asset_id;
	}
	
	public function getAsset(){
		$this->asset = new multimedia($this->asset_id);
		
		return $this->asset;
	}
	
	public function setNote($n){
		$this->note = $n;
	}
	
	public function setTime($t = ""){
		if($t == ""){
			$this->time = time();
		} else {
			$this->time = $t;
		}
	}
	
	public function setOwner(User $owner){
		$this->owner = $owner->getId();
	}
	
	public function getOwner(){
		if($this->o_user == null){
			$this->o_user = new User($this->owner);
		}
		return $this->o_user;
	}
	
	public function setAssetId($id){
		$this->asset_id = $id;
	}
	
	public function setAssetIdFromAsset(multimedia $asset){
		$this->asset_id = $asset->getId();
	}
}

?>