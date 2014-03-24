<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: db_adapter.php
* Directory	: system/database
* Author	: Eko Heri Susanto
* Description 	: fungsi common (CRUD) untuk database
*/
class db_adapter {
	private $driver = null;
	private $sql = '';
	public function __construct($driver){
		$this->driver = $driver;
		$this->driver->connect();
	}
	public function __destruct(){
		$this->driver->disconnect();
	}
	public function select($sql){
		$this->sql = $sql;
	}
	public function fetch_array(){
		return $this->driver->results($this->sql, 'array');
	}
	public function fetch_object(){
		return $this->driver->results($this->sql, 'object');
	}
	public function query($sql){
		return $this->driver->query($sql);
	}
	public function insert($table, $data){
		$sql = "INSERT INTO ".$table;
		$multi_rows = FALSE;
		foreach($data as $key => $val){
			if(!is_array($val) ) {
				$fields[$key] = $key;
				$rows[$key] = $this->escape($val);
			}else {
				$multi_rows = TRUE;
				foreach($val as $skey => $sval){
					$fields[$skey] = $skey;
					$row[$skey] = $this->escape($sval);
				}
				$rows[$key] = "(".implode(', ', $row).")";
			}
		}
		$sql .= " (". implode(', ', $fields).") VALUES ";
		if(!$multi_rows) $sql .= "(". implode(', ', $rows).");";
		else $sql .= implode(', ', $rows).";";
		return $this->driver->query($sql);
    	}
	public function update($table, $data, $where=NULL){
		$sql = "UPDATE ".$table." SET ";
		$values = array();
		foreach($data as $key => $val){
			$values[$key] = $key." = ".$this->escape($val);
		}
		$sql .= implode(", ", $values);
		if(is_array($where)){
			$filter = array();
			foreach($where as $key => $val){
				$filter[$key] = "(".$key." = ".$this->escape($val).")";
			}
			$sql .= " WHERE ".implode(" AND ", $filter);
		}
		return $this->driver->query($sql);
	}
	public function delete($table, $where=NULL){
		$sql = "DELETE FROM ".$table;
		if(is_array($where)){
			$filter = array();
			foreach($where as $key => $val){
				$filter[$key] = "(".$key." = ".$this->escape($val).")";
			}
			$sql .= " WHERE ".implode(" AND ", $filter);
		}
		return $this->driver->query($sql);
	}
	
	private function escape($str)
    	{
		if(is_string($str)){
			$str = str_replace("'", "''",$str);	
			return "'".$str."'";
		}

		if(is_integer($str))
		return (int)$str;

		if(is_double($str))
		return (double)$str;

		if(is_float($str))
		return (float)$str;

		if(is_bool($str))
		return ($str === FALSE) ? 0 : 1;

		if(is_null($str))
		return 'NULL';
    	}
}//end class
?>
