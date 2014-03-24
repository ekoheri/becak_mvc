<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: mysql.php
* Directory	: system/database/driver
* Author	: Eko Heri Susanto
* Description 	: driver mysql
*/
class mysql {
	private $conn;
	private $config;
	public function __construct($config){
		$this->config 	= $config;
	}
	public function connect() {
		extract($this->config);
		if(isset($this->conn)) return $this->conn;
		$this->conn = @mysql_connect($server,$user,$password, true) or show_error("Unable connect to mysql server.Please make sure the server, username or password specified in your Database.php file is valid."); 
		@mysql_select_db($database, $this->conn) or show_error("Unable to select your default database name.Please make sure the database name specified in your Database.php file is valid.");
	}
	public function disconnect(){
		if(isset($this->conn)) {
			@mysql_close($this->conn);
		}
	}
	public function query($sql){
		$result = @mysql_query($sql, $this->conn);
		if (!$result) {
    			show_error(mysql_error());
		}
		return $result;
	}
	public function results($query, $type = 'object'){
		$result = $this->query($query);
		$return = array();
		while ($row = @mysql_fetch_object($result)) {
		    if($type == 'array')
		        $return[] = (array) $row;
		    else
		        $return[] = $row;
		}
		@mysql_free_result($result);
		return @$return;
	}
}//end class
?>
