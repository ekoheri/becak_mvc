<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
* 
* File		: view_helper.php
* Directory	: system/helpers
* Author	: Eko Heri Susanto
* Description 	: fungsi untuk memuat (loaded) view atau user interface  
*/
function app_view($view_name, $arr_data = array(), $return_string=false){
	$file = APP . 'views'. DS . $view_name . PHP_EXT;
	return _view($file, $arr_data, $return_string);
}//end view

function view($view_name, $arr_data = array(), $return_string=true){
	$file = DIR.$GLOBALS['d'] .DS. 'views'. DS . $view_name . PHP_EXT;
	return _view($file, $arr_data, $return_string);
}

function app_script($filename, $data = '') {   
    return _script(APP .'scripts'. DS, $filename, $data);
}

function script($filename, $data = '') {   
    return _script(DIR .$GLOBALS['d']. DS .'scripts'. DS, $filename, $data);
}

function _view($file, $arr_data = array(), $return_string){
	if (!file_exists($file)) throw new Exception("Can't load template file: " . $file);	

	if(sizeof($arr_data) > 0) extract($arr_data, EXTR_SKIP);
	ob_start();
	include ($file);
	if($return_string){
		$content = ob_get_contents();
		@ob_end_clean();
		return $content;
	}
	common::register('Output')->append_output(ob_get_contents());
	@ob_end_clean();
    	return;
}
function _script($path, $filename, $data = '')
{
    if( empty($data) ) $data = array();
    
    if ( ! file_exists($path . $filename . PHP_EXT) )
    {
        throw new Exception('Unable locate the script file: '. $path . $filename . PHP_EXT);
    } 
    
    if(sizeof($data) > 0) { extract($data, EXTR_SKIP); }
    
    ob_start();
    
    include($path . $filename . PHP_EXT);
    $content = ob_get_contents();
    
    ob_end_clean();
    return "\n".$content; 
}
?>
