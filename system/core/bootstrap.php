<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: becak.php
* Directory	: system/core
* Author	: Eko Heri Susanto
* Description 	: routine untuk memuat (loaded) system utama serta routine untuk menjalankan default controller
*/

//panggil class inti (core)


require (SYS.'constant'.DS.'file.php'); 
require (SYS.'core'.DS.'common.php');
require (SYS .'error'. DS .'errors.php'); 
require (SYS.'libraries'. DS .'benchmark_library.php');
$benchmark = common::register('Benchmark');
$benchmark->mark('total_execution_time_start');

require (SYS.'core'.DS.'loader.php');
require (SYS.'core'.DS.'base.php');
require (SYS.'core'.DS.'controller.php');  
require (SYS.'core'.DS.'model.php');

require (SYS.'libraries'.DS.'router_library.php');  
require (SYS.'libraries'.DS.'uri_library.php');
require (SYS.'libraries'.DS.'output_library.php');
require (SYS.'libraries'.DS.'config_library.php');


date_default_timezone_set(common::config_item('timezone_set'));

$uri 	= common::register('Uri');
$router = common::register('Router');
$output = common::register('Output');

if ($output->_display_cache($uri) == TRUE) { exit; }

$GLOBALS['d']   = $router->fetch_directory();   // Get requested directory
$GLOBALS['c']   = $router->fetch_class();       // Get requested controller
$GLOBALS['m']   = $router->fetch_method();      // Get requested method

//panggil default controller pada directory application/controller
if(! file_exists(DIR.$GLOBALS['d'].DS.'controllers'.DS.$GLOBALS['c'].PHP_EXT)) {
	if($router->query_string) 
		show_404("{$GLOBALS['d']}/{$GLOBALS['c']}/{$GLOBALS['m']}");
            
        throw new Exception('Unable to load your default controller.Please make sure the controller specified in your Routes.php file is valid.');
}
require (DIR.$GLOBALS['d'].DS.'controllers'.DS.$GLOBALS['c'].PHP_EXT);

if ( ! class_exists($GLOBALS['c']) 
OR $GLOBALS['m'] == 'controller'
OR in_array(strtolower($GLOBALS['m']), array_map('strtolower', get_class_methods('Controller')))
){
    show_404("Class {$GLOBALS['d']}/{$GLOBALS['c']}/{$GLOBALS['m']}");
}

$MC = new $GLOBALS['c']();

if ( ! in_array(strtolower($GLOBALS['m']), array_map('strtolower', get_class_methods($MC)))){
    show_404("{$GLOBALS['d']}/{$GLOBALS['c']}/{$GLOBALS['m']}");
}

call_user_func_array(array($MC, $GLOBALS['m']), array_slice($uri->rsegments, 3));

$output->_display();

while (ob_get_level() > 0) { ob_end_flush(); }
?>

