<?php
defined('SYS') or exit('Access Denied!');
/* 
Isi 0 atau 1. Jika sudah online ganti dengan 0
*/
$config['display_errors']        = 1; 
/**
| @see  http://php.net/manual/en/function.date-default-timezone-set.php
| @link http://www.php.net/manual/en/timezones.php
*/
$config['timezone_set']          = 'Asia/Jakarta';

$config['base_url']              = 'http'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '').'://'.$_SERVER['HTTP_HOST'].str_replace('//','/',dirname($_SERVER['SCRIPT_NAME']).'/');
$config['source_url']            = $config['base_url']."/sources/";
$config['index_page']            = "index.php";
$config['uri_protocol']          = "AUTO";
$config['url_suffix']            = "";
$config['language']              = "english";
$config['charset']               = "UTF-8";
$config['subclass_prefix']       = 'MY_';
$config['permitted_uri_chars']   = 'a-z 0-9~%.:_\-';
$config['enable_query_strings']  = FALSE;
$config['directory_trigger']     = 'd';   
$config['controller_trigger']    = 'c';
$config['function_trigger']      = 'm';
$config['log_threshold']         = 0;
$config['log_path']              = '';
$config['log_date_format']       = 'Y-m-d H:i:s';
$config['encryption_key']        = "";
$config['sess_cookie_name']      = 'becak_session';
$config['sess_expiration']       = 7200;
$config['sess_encrypt_cookie']   = FALSE;
$config['sess_driver']           = 'cookie';  // or database
$config['sess_db_var']           = 'db';            
$config['sess_table_name']       = 'becak_sessions';
$config['sess_match_ip']         = FALSE;
$config['sess_match_useragent']  = TRUE;
$config['sess_time_to_update']   = 300;
$config['cookie_prefix']         = "";
$config['cookie_domain']         = "";
$config['cookie_path']           = "/";
$config['cookie_time']           = (7 * 24 * 60 * 60);
$config['global_xss_filtering']  = FALSE;
$config['time_reference']        = 'local';
$config['proxy_ips']             = '';
?>
