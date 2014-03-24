<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: model.php
* Directory	: system/core
* Author	: Eko Heri Susanto
* Description 	: class model ini adalah induk dari semua model
*/

class model extends base {
	public function __construct(){
		$this->_assign_db_objects();
	}
	public function _assign_db_objects(){
		foreach(base::$databases as $key){
		    $this->$key = &base::getInstance()->$key;
		}
	}
	
}//end class model
?>
