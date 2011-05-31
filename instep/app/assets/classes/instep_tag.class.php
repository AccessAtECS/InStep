<?
class instep_tag {
	private $id = null;
	private $name;
	
	
	private $m_db;
	
	const TYPE_ID = 1;
	const TYPE_NAME = 2;
	
	function __construct($id = null, $type = instep_tag::TYPE_ID) {
		if($id == null) return;
		

		$is = new InStep();
		$this->m_db = $is->db();
		
		switch($type){
		
			case instep_tag::TYPE_ID:
				if(is_int($id) == false) throw new Exception("Tag ID is not an integer");
				$this->id = $id;
				$tag = $this->m_db->single("SELECT * FROM tag WHERE id = $this->id");
				if(empty($tag)) throw new Exception("No tag with that ID.", 404);
				$this->name = $tag[0]['name'];
			break;
			
			case instep_tag::TYPE_NAME:				
				$tag = $this->m_db->single("SELECT * FROM tag WHERE name = LOWER('" . $this->m_db->real_escape_string(strtolower($id)) . "')");
				if(empty($tag)) throw new Exception("No tag with that ID.", 404);
				$this->id = $tag[0]['id'];
				$this->name = $id;			
			break;
		
		
		}

		$this->name = $tag[0]['name'];	
	}
	
	public function commit(){
		$data['name'] = $this->name;

		$is = new InStep();
		$this->m_db = $is->db();

		if($this->id == null) {
			$s = $this->m_db->insert($data, "tag");
		} else {
			$this->m_db->update($data, "tag", array(array("", "id", $this->id)));
		}
		$this->m_db->runBatch();
		if($this->id == null) $this->id = $this->m_db->insert_id;	
		
		return $this->id;
	}
	
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($n){
		$this->name = $n;
	}
}