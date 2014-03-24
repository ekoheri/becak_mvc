<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: db_factory.php
* Directory	: system/database
* Author	: Eko Heri Susanto
* Description 	: penyedia koneksi ke bermacam-macam database
*/
class db_factory {
	public static function callDB($db_name){
		include APP.'config'.DS.'database'.PHP_EXT;
		if(!is_array($config[$db_name])) 
			throw new Exception("Please set a valid database driver from config database file");
		$driver_class = strtolower($config[$db_name]['type']);
		include SYS.'database'.DS.'driver'.DS.$driver_class.PHP_EXT;
		include SYS.'database'.DS.'db_adapter'.PHP_EXT;
        	$driver = new $driver_class($config[$db_name]); 
		$adapter = new db_adapter($driver);
		return $adapter;
	}//end callDB
}//end class
?>
