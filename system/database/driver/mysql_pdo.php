<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: pdo_mysql.php
* Directory	: system/database/driver
* Author	: Eko Heri Susanto
* Description 	: driver PDO mysql
*/
class mysql_pdo {
	private $conn;
	private $config;
	public function __construct($config){
		$this->config 	= $config;
	}
	public function connect() {
		extract($this->config);
		if(isset($this->conn)) return $this->conn;
		try{
		    $this->conn = new PDO ("mysql:host=$server;dbname=$database",$user,$password);
		}catch(PDOException $e){
		    echo __LINE__.$e->getMessage();
		}
	}
	public function disconnect(){
		if(isset($this->conn)) {
			$this->conn = null;
		}
	}
	public function query($sql){
		$result = array();
		try{
		    $result = $this->conn->exec($sql) or print_r($this->conn->errorInfo());
		}catch(PDOException $e){
		    echo __LINE__.$e->getMessage();
		}
		return $result;
	}
	public function results($query, $type = 'object'){
		$result = $this->conn->query($query) or print_r($this->conn->errorInfo());
		$result->setFetchMode(PDO::FETCH_ASSOC);
		return $result;
	}
}//end class
?>
