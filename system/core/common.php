<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: common.php
* Directory	: system/core
* Author	: Eko Heri Susanto
* Description 	: class umum
*/

//class registry digunakan untuk mendaftarkan variable menjadi object
class common {
	private static $object  = array();
	private static $instance;

	//singleton
	private static function singleton(){
		if(!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;	
	}
	private function get($id){
		if(isset(self::$object[$id])){
		    return self::$object[$id];
		}
		return NULL;
	}
	private function set($id, $value){
		self::$object[$id] = $value;
	}
	private static function getObject($id) {
		return self::singleton()->get($id);
	}
	private static function setObject($id, $instance){
		return self::singleton()->set($id, $instance);
	}
	
	private static function get_static($filename = 'config', $var = '', $folder = '')
	{
	    static $static = array();

	    if ( ! isset($static[$filename]))
	    {
		if ( ! file_exists($folder. DS .$filename. PHP_EXT)) 
			throw new Exception('The static file '. DS .$folder. DS .$filename. PHP_EXT .' does not exist.');
		
		require($folder. DS .$filename. PHP_EXT);
		
		if($var == '') $var = &$filename;
		
		if ( ! isset($$var) OR ! is_array($$var))
		{
		    throw new Exception('The static file '. DS .$folder. DS .$filename. PHP_EXT .' file does not appear to be formatted correctly.');
		}
	    
		$static[$filename] =& $$var;
	     }
	     
	    return $static[$filename];    
	}
	public static function get_config($filename = 'config', $var = '')
	{
	    return self::get_static($filename, $var, APP. DS. 'config');
	}

	/* Fungsi register digunakan untuk mendaftarkan variable menjadi object.*/
	public static function register($ClassName){
		$Obj = self::singleton();
		$ClassName = strtolower($ClassName);
		if($Obj->getObject($ClassName) != NULL){
			return $Obj->getObject($ClassName);	
		} 
		$Class = $ClassName;
		$Obj->setObject($ClassName, new $Class());
		$Object = $Obj->getObject($ClassName);
		if(is_Object($Object)) return $Object;
	}
	public static function config_item($item, $config_name = 'config')
	{
	    static $config_item = array();

	    if ( ! isset($config_item[$item]))
	    {
		$config_name = self::get_config($config_name);
		if ( ! isset($config_name[$item])) return FALSE;
		
		$config_item[$item] = $config_name[$item];
	    }
	    return $config_item[$item];
	}
	public static function is_really_writable($file)
	{    
	    // If we're on a Unix server with safe_mode off we call is_writable
	    if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
	    {
		return is_writable($file);
	    }

	    // For windows servers and safe_mode "on" installations we'll actually
	    // write a file then read it.  Bah...
	    if (is_dir($file))
	    {
		$file = rtrim($file, '/').'/'.md5(rand(1,100));

		if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
		    return FALSE;
		}

		fclose($fp);
		@chmod($file, DIR_WRITE_MODE);
		@unlink($file);
		return TRUE;
	    }
	    elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
	    {
		return FALSE;
	    }

	    fclose($fp);
	    return TRUE;
	}
}//end class
?>
