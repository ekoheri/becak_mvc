<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: loader.php
* Directory	: system/core
* Author	: Eko Heri Susanto
* Description 	: class loader berfungsi untuk memuat (loaded) library, helper, view, model  
*/
class loader {
	private static $helpers = array();
	private static $sys_helpers = array();
	public static function sys_library($library){
		self::_library($library, 'system');
	}
	public static function app_library($library){
		self::_library($library, 'application');
	}
	public static function library($library){
		self::_library($library, 'directories');
	}
	private static function _library($library, $folder = 'system'){
		if($library=='')return false;
		$library = strtolower($library);
		switch($folder){
			case 'system' 		: $lib_folder = SYS.'libraries'.DS;break;
			case 'application' 	: $lib_folder = APP.'libraries'.DS;break;
			case 'directories' 	: $lib_folder = DIR.$GLOBALS['d'].DS.'libraries'.DS;break;
		}
		if(file_exists($lib_folder.$library.'_library'.PHP_EXT )) {	
			require ($lib_folder.$library.'_library'.PHP_EXT );
			base::getInstance()->$library = common::register($library);
		} else throw new Exception("Can't load library file: " . $library);
	}
	public static function sys_helper($helper) {
		self::_helper($helper, 'system');
    	}
	public static function app_helper($helper='') {
		self::_helper($helper, 'application');
	}
	public static function helper($helper =''){
		self::_helper($helper, 'directories');
	}
	private static function _helper($helper, $folder = 'system') {
		if($helper=='')return false;
		if( isset(self::$helpers[$helper]) ){
		    return; 
		}
		$helper = strtolower($helper);
		switch($folder){
			case 'system' 		: $hlp_folder = SYS.'helpers'. DS;break;
			case 'application' 	: $hlp_folder = APP.'helpers'. DS;break;
			case 'directories' 	: $hlp_folder = DIR.$GLOBALS['d'].DS.'helpers'.DS;break;
		}
		if(file_exists($hlp_folder . $helper.'_helper'. PHP_EXT)){
		    include($hlp_folder . $helper.'_helper'. PHP_EXT);
		    self::$helpers[$helper] = $helper;
		    return;    
		} else throw new Exception("Can't load helper file: " . $helper);
	}
	public static function app_model($model_name){
		self::_model($model_name, 'application');
	}
	public static function model($model_name){
		self::_model($model_name, 'directories');
	}
	private static function _model($model_name, $folder = 'system'){
		if($model_name=='')return;
		$model_name = strtolower($model_name);
		$model_var = &$model_name;
        	if (isset(base::getInstance()->$model_var) AND is_object(base::getInstance()->$model_var))
		{ return; }
		switch($folder){
			case 'application' 	: $mdl_folder = APP.'models'.DS;break;
			case 'directories' 	: $mdl_folder = DIR.$GLOBALS['d'].DS.'models'.DS;break;
		}
		if(file_exists($mdl_folder.$model_name.PHP_EXT )) {	
			require ($mdl_folder.$model_name.PHP_EXT );
			base::getInstance()->$model_var = new $model_name();
			base::getInstance()->$model_var->_assign_db_objects();
	        	base::$models[$model_var] = base::getInstance()->$model_var;
		} else throw new Exception("Can't load model file: " . $model);
	}
	public static function database($db_name = 'db'){
		$db_var = $db_name;
		$obj = base::getInstance();
		if (isset($obj->{$db_var}) AND is_object($obj->{$db_var}) ) {
		    return;
		}
		require (SYS .'database'. DS .'db_factory'. PHP_EXT);
 		$obj->{$db_var} = db_factory::callDB($db_name);
        	base::$databases[$db_var] = $db_var; 
		if (count(base::$models) >= 0) {
			foreach (base::$models as $model_name)
			    $obj->$model_name->$db_var = &$obj->$db_var;
		}
	}
}//end class loader
?>
