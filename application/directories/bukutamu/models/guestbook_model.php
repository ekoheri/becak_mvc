<?php defined('SYS') or exit('Access Denied!');
class guestbook_model extends model {
	public function __construct(){
		loader::database("db");
	}
	function GetData($where=""){
		$this->db->select("SELECT * FROM `guestbook` ".$where);
		return $this->db->fetch_array();
	}
	function InsertData($data){
		return $this->db->insert('guestbook', $data);
	}
	function UpdateData($data, $where){
		return $this->db->update('guestbook', $data, $where);
	}
	function DeleteData($where){
		return $this->db->delete('guestbook', $where);
	}
}
?>
