<?php

class comparison {

	private $d_id = null;
	
	private $d_id_from;
	private $d_id_to;
	private $d_date = null;
	private $d_owner;
	private $d_notes;
	private $d_learner = null;
	
	private $o_owner;
	private $o_from;
	private $o_to;
	private $o_learner = null;
	
	public function __construct($id = null){
		if($id == null) return;
		$this->d_id = $id;
		
		$is = new InStep();
		
		// Set up database connections
		$this->m_is_database = $is->db();
		
		$p = $this->m_is_database->single("SELECT * FROM comparison WHERE id =" . $this->m_is_database->real_escape_string($id));
		if(!empty($p)) {
			$this->d_id_to = $p[0]['idto'];
			$this->d_id_from = $p[0]['idfrom'];
			$this->d_date = new DateTime($p[0]['date'], new DateTimeZone('Europe/London'));
			$this->d_owner = $p[0]['owner'];
			$this->d_notes = $p[0]['notes'];
			$this->d_learner = $p[0]['learner'];
			
			// Set up objects
			$this->o_owner = new User($this->d_owner);
			$this->o_from = new multimedia($this->d_id_from);
			$this->o_to = new multimedia($this->d_id_to);
			if(!empty($this->d_learner)) $this->o_learner = new User($this->d_learner);
			
			
		} else {
			throw new Exception("No comparison with that ID!");
		}
	}
	
	
	public function commit(){
		$is = new InStep();
		$db = $is->db();
	
		$data['idfrom'] = $this->d_id_from;
		$data['idto'] = $this->d_id_to;
		if($this->d_date != null) $data['date'] = $this->d_date;
		$data['owner'] = $this->d_owner;
		$data['notes'] = $this->d_notes;
		if($this->d_learner != null && $this->d_learner != "") $data['learner'] = $this->d_learner;

		
		if($this->d_id == null) {
			$s = $db->insert($data, "comparison");
		} else {
			$db->update($data, "comparison", array(array("", "id", $this->id)));
		}
		
		$db->runBatch();
		if($this->d_id == null) $this->d_id = $db->insert_id;	
		
		return $this->d_id;	
	}
	
	public function getId(){
		return $this->d_id;
	}
	
	public function getIdFrom(){
		return $this->d_id_from;
	}
	
	public function getIdTo(){
		return $this->d_id_to;
	}
	
	public function getDate(){
		return $this->d_date;
	}
	
	public function getOwnerId(){
		return $this->d_owner;
	}
	
	public function getOwner(){
		return $this->o_owner;
	}

	public function getNotes(){
		return $this->d_notes;
	}
	
	public function getFrom(){
		return $this->o_from;
	}
	
	public function getTo(){
		return $this->o_to;
	}
	
	public function getLearner(){
		return $this->o_learner;
	}
	
	public function setIdFrom($n){
		$this->d_id_from = $n;
	}
	
	public function setIdTo($n){
		$this->d_id_to = $n;
	}
	
	public function setDate($n){
		$this->d_date = $n;
	}
	
	public function setOwnerId($n){
		$this->d_owner = $n;
	}
	
	public function setNotes($n){
		$this->d_notes = $n;
	}
	
	public function setOwner(User $n){
		$this->o_owner = $n;
		$this->setOwnerId($this->o_owner->getId());
	}

	public function setLearner(User $n){
		$this->o_learner = $n;
		$this->d_learner = $n->getId();
	}

}

?>