<?php defined('SYS') or exit('Access Denied!');
/* 
* Becak MVC Framework version 1.0 
*
* File		: controller.php
* Directory	: system/core
* Author	: Eko Heri Susanto
* Description 	: class controller ini adalah induk dari semua controller
*/
require(APP .'globals'. DS .'global_controller'. PHP_EXT); 

class controller extends global_controller {
	public function __construct(){
		parent::__construct();
		$this->_init();	
	}
	private function _init()
    	{
        $Classes = array(                         
                            'config'    => 'Config',
                            'router'    => 'Router',
                            'uri'       => 'URI', 
                            'output'    => 'Output' 
                        );
                        
        foreach ($Classes as $public_var => $Class)
        {
            $this->$public_var = common::register($Class);
        }
    }
}//end class
?>
