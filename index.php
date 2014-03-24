<?php
error_reporting(E_ALL | E_STRICT); 
define('DS',   DIRECTORY_SEPARATOR); 
define('SYS',  'system'.DS); 
define('APP',  'application'.DS);
define('DIR',  APP . 'directories'.DS);
define('PHP_EXT',  '.php');
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
if ( get_magic_quotes_gpc() ) {
    
    function stripslashes_gpc(&$value){
        $value = stripslashes($value);
    }
    
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}
require(SYS .'core'.DS.'bootstrap.php'); 
?>
