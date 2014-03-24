<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: base.php
* Directory	: system/core
* Author	: Eko Heri Susanto
* Description 	: class base adalah class yang digunakan untuk menyimpan instance object
*/
class base {
	private static $instance;
	public static $helpers = array();
	public static $models = array();
	public static $databases = array();

	public function __construct(){
		self::$instance = &$this;
	}
	public static function getInstance(){
		return self::$instance;
	}
}

function this(){ return base::getInstance(); }
?>
