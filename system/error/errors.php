<?php
defined('SYS') or exit('Access Denied!');
function Err_ErrorTemplate($errno, $errstr, $errfile, $errline, $type)
{  
    ob_start();
    include(SYS .'error'. DS .'err_template'. PHP_EXT);
    $buffer = ob_get_contents(); 
    ob_end_clean();
    
   // log_php_errors($type, $errstr, $errfile, $errline);
    
    echo $buffer;
}
/*function log_php_errors($type, $errstr, $errfile, $errline)
{    
    log_message('error', 'Php Error Type: '.$type.'  --> '.$errstr. ' '.$errfile.' '.$errline, TRUE);
}*/
function Err_ExceptionHandler($e)
{   
    $type = 'Exception';
    $sql  = '';
        
    if(substr($e->getMessage(),0,3) == 'SQL') 
    {
        $ob   = ob::instance();
        $type = 'Database';
        
        foreach($ob->_dbs as $key => $val)
        {
           if(is_object($ob->$key))
           $sql .= $ob->{$key}->last_query($ob->{$key}->prepare); 
        }        
    }
    
    ob_start();
    include(SYS .'error'. DS .'err_exception'. PHP_EXT);
    $buffer = ob_get_contents(); 
    ob_end_clean();
    
    //log_php_errors('Exception', $e->getMessage(), $e->getFile(), $e->getLine());
    
    echo $buffer;
}       
function show_404($page = '')
{   
    //log_message('error', '404 Page Not Found --> '.$page);
    echo show_http_error('404 Page Not Found', $page, 'err_404', 404);

    exit;
}
function show_error($message, $status_code = 500)
{
    //log_message('error', 'HTTP Error --> '.$message); 
    echo show_http_error('An Error Was Encountered', $message, 'err_general', $status_code);
    
    exit;
}

function show_http_error($heading, $message, $template = 'ob_general', $status_code = 500)
{
    set_status_header($status_code);

    $message = implode('<br />', ( ! is_array($message)) ? array($message) : $message);
    
    ob_start();
    include(SYS. 'error'. DS .$template. PHP_EXT);
    $buffer = ob_get_contents(); 
    ob_end_clean();
    
    return $buffer;
}

function Err_ErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (($errno & error_reporting()) == 0) return;  
    
    switch ($errno)
    {
        case E_ERROR:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "ERROR");
        break;
        
        case E_WARNING:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "WARNING");
        break;
        
        case E_PARSE:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "PARSE ERROR");
        break;
        
        case E_NOTICE:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "NOTICE");
        break;
                           
        case E_CORE_ERROR:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "CORE ERROR");
        break;
        
        case E_CORE_WARNING:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "CORE WARNING");
        break;
        
        case E_COMPILE_ERROR:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "COMPILE ERROR");
        break;
        
        case E_USER_ERROR:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "USER FATAL ERROR");
            exit();
        break;   
            
        case E_USER_WARNING:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "USER WARNING");
        break;
        
        case E_USER_NOTICE:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "USER NOTICE");
        break;
        
        case E_STRICT:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "STRICT ERROR");
        break;
        
        case E_RECOVERABLE_ERROR:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "RECOVERABLE ERROR");
        break;
        
        case E_DEPRECATED:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "DEPRECATED ERROR");
        break;
        
        case E_USER_DEPRECATED:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "USER DEPRECATED ERROR");
        break;
        
        case E_ALL:
            Err_ErrorTemplate($errno, $errstr, $errfile, $errline, "ERROR");
        break;
    
    }
    
    return TRUE;    // return true and don't execute internal error handler 
}          

function set_status_header($code = 200, $text = '')
{
    $stati = array(
                        200    => 'OK',
                        201    => 'Created',
                        202    => 'Accepted',
                        203    => 'Non-Authoritative Information',
                        204    => 'No Content',
                        205    => 'Reset Content',
                        206    => 'Partial Content',

                        300    => 'Multiple Choices',
                        301    => 'Moved Permanently',
                        302    => 'Found',
                        304    => 'Not Modified',
                        305    => 'Use Proxy',
                        307    => 'Temporary Redirect',

                        400    => 'Bad Request',
                        401    => 'Unauthorized',
                        403    => 'Forbidden',
                        404    => 'Not Found',
                        405    => 'Method Not Allowed',
                        406    => 'Not Acceptable',
                        407    => 'Proxy Authentication Required',
                        408    => 'Request Timeout',
                        409    => 'Conflict',
                        410    => 'Gone',
                        411    => 'Length Required',
                        412    => 'Precondition Failed',
                        413    => 'Request Entity Too Large',
                        414    => 'Request-URI Too Long',
                        415    => 'Unsupported Media Type',
                        416    => 'Requested Range Not Satisfiable',
                        417    => 'Expectation Failed',

                        500    => 'Internal Server Error',
                        501    => 'Not Implemented',
                        502    => 'Bad Gateway',
                        503    => 'Service Unavailable',
                        504    => 'Gateway Timeout',
                        505    => 'HTTP Version Not Supported'
                    );

    if ($code == '' OR ! is_numeric($code))
    {
        show_error('Status codes must be numeric', 500);
    }

    if (isset($stati[$code]) AND $text == '')
    {                
        $text = $stati[$code];
    }
    
    if ($text == '')
    {
        show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
    }
    
    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

    if (substr(php_sapi_name(), 0, 3) == 'cgi')
    {
        header("Status: {$code} {$text}", TRUE);
    }
    elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
    {
        header($server_protocol." {$code} {$text}", TRUE, $code);
    }
    else
    {
        header("HTTP/1.1 {$code} {$text}", TRUE, $code);
    }
}

set_error_handler('Err_ErrorHandler');
set_exception_handler('Err_ExceptionHandler');
?>
