<?php  defined('SYS') or exit('Access Denied!');
class global_controller extends base {
	public $base, $base_url, $base_img;
      	public $title, $head, $meta;
	public $calendar='';
	public function  __construct(){
		parent::__construct();
		loader::sys_helper('view');
		loader::sys_helper('head_tag');
		$this->base     = common::config_item('base_url');
		$this->base_url = common::config_item('base_url')  . common::config_item('index_page');
		$this->base_img = common::config_item('source_url').'images/';
		$this->meta     = meta('Content-type', 'text/html; charset=utf-8', 'equiv');
		$this->meta    .= meta('author', 'Eko Heri Susanto');
	}
}
?>
