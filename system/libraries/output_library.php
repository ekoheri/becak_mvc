<?php defined('SYS') or exit('Access Denied!');
class Output {
    public $final_output;
    public $cache_expiration    = 0;
    public $headers             = array();
    public $enable_profiler     = FALSE;
    public $parse_exec_vars     = TRUE;    

    public function __construct()
    {
        //log_message('debug', "Output Class Initialized");
    }
  
    public function get_output()
    {
        return $this->final_output;
    }
    public function set_output($output)
    {
        $this->final_output = $output;
    }

    public function append_output($output)
    {
        if ($this->final_output == '')
        {
            $this->final_output = $output;
        }
        else
        {
            $this->final_output .= $output;
        }
    }

    public function set_header($header, $replace = TRUE)
    {
        $this->headers[] = array($header, $replace);
    }
 
    public function set_status_header($code = 200, $text = '')
    {
        set_status_header($code, $text);
    }
  
    public function enable_profiler($val = TRUE)
    {
        exit('Profiler Class Not Implemented Yet ! Use this->db->last_query() for now.');
        
        $this->enable_profiler = (is_bool($val)) ? $val : TRUE;
    }
    public function cache($time)
    {
        $this->cache_expiration = ( ! is_numeric($time)) ? 0 : $time;
    }
    public function _display($output = '')
    {    
        if ($output == '')
        {
            $output =& $this->final_output;
        }
        if ($this->cache_expiration > 0)
        {
            $this->_write_cache($output);
        }
        $elapsed = common::register('Benchmark')->elapsed_time('total_execution_time_start', 'total_execution_time_end');        
        $output  = str_replace('{elapsed_time}', $elapsed, $output);
	$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).' MB';
	$output = str_replace('{memory_usage}', $memory, $output);
       
        // --------------------------------------------------------------------
        
        // Is compression requested?
        /*if (common::config_item('compress_output', 'cache') === TRUE)
        {
            if (extension_loaded('zlib'))
            {             
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
                {   // Obullo changes .. 
                    ini_set('zlib.output_compression_level', config_item('compression_level', 'cache'));  
                    ob_start('ob_gzhandler');
                }
            }
        }
	*/
        if (count($this->headers) > 0)
        {
            foreach ($this->headers as $header)
            {
                @header($header[0], $header[1]);
            }
        }        
        if ( ! function_exists('this'))
        {
            echo $output;
            return TRUE;
        }
        if ($this->enable_profiler == TRUE)
        {
            $profiler = base::register('Profiler');                
            if (preg_match("|</body>.*?</html>|is", $output))
            {
                $output  = preg_replace("|</body>.*?</html>|is", '', $output);
                $output .= $profiler->run();
                $output .= '</body></html>';
            }
            else
            {
                $output .= $profiler->run();
            }
        }
        $ob = base::getInstance();
        
        if (method_exists($ob, '_output'))
        {
            $ob->_output($output);
        }
        else
        {
            echo $output;  // Send it to the browser!
        }
    }
    public function _write_cache($output)
    {
        $OB = base::getInstance(); 
        
        //$path = common::config_item('cache_path', 'cache');
    
        $cache_path = APP.'cache'.DS; //= ($path == '') ?  APP.'system'.DS.'cache'.DS : $path;
        
        if ( ! is_dir($cache_path) OR ! common::is_really_writable($cache_path))
        {
		throw new Exception('Unable to write cache file: '.$cache_path);            
		return;
        }
        
        $uri =  $OB->config->item('base_url').$OB->config->item('index_page').$OB->uri->uri_string();
        $cache_path .= md5($uri);

        if ( ! $fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))//FOPEN_WRITE_CREATE_DESTRUCTIVE))
        {
	    throw new Exception('Unable to write cache file: '.$cache_path);
            return;
        }
        $expire = time() + ($this->cache_expiration * 60);
        
        if (flock($fp, LOCK_EX))
        {
            fwrite($fp, $expire.'TS--->'.$output);
            flock($fp, LOCK_UN);
        }
        else
        {
	    throw new Exception('Unable to secure a file lock for file at: '.$cache_path);	
            return;
        }
        fclose($fp);
        @chmod($cache_path, DIR_WRITE_MODE);

        //log_message('debug', "Cache file written: ".$cache_path);
    }
    public function _display_cache(&$URI)
    {
        //$cache_path = (common::config_item('cache_path', 'cache') == '') ? APP.'system'.DS.'cache'.DS : common::config_item('cache_path', 'cache');
        $cache_path =  APP.'cache'.DS;
        // Build the file path.  The file name is an MD5 hash of the full URI
        $uri =  common::config_item('base_url').
	common::config_item('index_page').$URI->uri_string;
         
        $filepath = $cache_path . md5($uri);
      
        if ( ! @file_exists($filepath))
        {
		return FALSE;
        }
    
        if ( ! $fp = @fopen($filepath, "r"))
        {
            	return FALSE;
        }
           
        flock($fp, LOCK_SH);
        
        $cache = '';
        if (filesize($filepath) > 0)
        {
            $cache = fread($fp, filesize($filepath));
        }
    
        flock($fp, LOCK_UN);
        fclose($fp);
                    
        // Strip out the embedded timestamp        
        if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
        {
            return FALSE;
        }
        
        // Has the file expired? If so we'll delete it.
        if (time() >= trim(str_replace('TS--->', '', $match['1'])))
        {         
            if (common::is_really_writable($cache_path))
            {
                @unlink($filepath);
                //log_message('debug', "Cache file has expired. File deleted");
                return FALSE;
            }
        }

        // Display the cache
        $this->_display(str_replace($match['0'], '', $cache));
        return TRUE;
    }
}//end class
?>
