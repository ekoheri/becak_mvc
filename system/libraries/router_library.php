<?php defined('SYS') or exit('Access Denied!');
class Router {
    
    public $config;    
    public $routes              = array();
    public $error_routes        = array();
    public $class               = '';
    public $method              = 'index';
    public $directory           = '';
    public $uri_protocol        = 'auto';
    public $default_controller;
    public $query_string        = FALSE; // Obullo  1.0 changes
    
    public function __construct()
    {
        //$routes = get_config('routes');   // Obullo changes..
	//print_r($routes);
        
	require(APP. 'config'.DS.'routes'. PHP_EXT);
        $this->routes = ( ! isset($routes) OR ! is_array($routes)) ? array() : $routes;
	//print_r($this->routes);
        unset($routes);
        
        $this->method = $this->routes['index_method'];
        $this->uri    = common::register('Uri');
        $this->_set_routing();        
                
        //log_message('debug', "Router Class Initialized");
    }
    
    private function _set_routing()
    {
        if (common::config_item('enable_query_strings') === TRUE AND isset($_GET[config_item('controller_trigger')]))
        {
            $this->query_string = TRUE;
        
            $this->set_directory(trim($this->uri->_filter_uri($_GET[config_item('directory_trigger')])));
            $this->set_class(trim($this->uri->_filter_uri($_GET[config_item('controller_trigger')])));

            if (isset($_GET[config_item('function_trigger')]))
            {
                $this->set_method(trim($this->uri->_filter_uri($_GET[config_item('function_trigger')]))); 
            }
            
            return;
        }

        $this->default_controller = ( ! isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);    
        
        $this->uri->_fetch_uri_string();
        
        if ($this->uri->uri_string == '')
        {
            if ($this->default_controller === FALSE)
            {
                throw new Exception("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
            }

            $segments = $this->_validate_request(explode('/', $this->default_controller)); 
            $this->set_class($segments[1]);
            $this->set_method($this->routes['index_method']);  // index
            $this->uri->rsegments = $segments;
            $this->uri->_reindex_segments();
            
            //log_message('debug', "No URI present. Default controller set.");
            return;
        }
        unset($this->routes['default_controller']);
        
        $this->uri->_remove_url_suffix();
        $this->uri->_explode_segments();
        $this->_parse_routes();        
        $this->uri->_reindex_segments();
    }
    private function _set_request($segments = array())
    {   
        $segments = $this->_validate_request($segments);
        
        if (count($segments) == 0)
        return;
                        
        $this->set_class($segments[1]);
        
        if (isset($segments[2]))
        {
                $this->set_method($segments[2]);   
        }
        else
        {
            $segments[2] = $this->routes['index_method'];
        }
        $this->uri->rsegments = $segments;
    }
    private function _validate_request($segments)
    {
        if( ! isset($segments[0]) ) $segments[0] = '';
        if( ! isset($segments[1]) ) $segments[1] = '';
        if (is_dir(DIR.$segments[0]))
        {  
            $this->set_directory($segments[0]);
            
            if( ! empty($segments[1])) 
            {
                if (file_exists(DIR.$segments[0].DS.'controllers'.DS.$segments[1].PHP_EXT))
                return $segments;  
            }

        }

        show_404($segments[0].' / '.$segments[1]);
	echo 'Error '.$segments[0].' / '.$segments[1];
    }
    private function _parse_routes()
    {
        if (count($this->routes) == 1)
        {             
            $this->_set_request($this->uri->segments);
            return;
        }

        $uri = implode('/', $this->uri->segments);

        if (isset($this->routes[$uri]))
        {
            $this->_set_request(explode('/', $this->routes[$uri]));        
            return;
        }
                
        foreach ($this->routes as $key => $val)
        {                        
            $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));
            
            if (preg_match('#^'.$key.'$#', $uri))
            {            
                if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                {
                    $val = preg_replace('#^'.$key.'$#', $val, $uri);
                }
            
                $this->_set_request(explode('/', $val));        
                return;
            }
        }
        $this->_set_request($this->uri->segments);
    }

    public function set_class($class)
    {
        $this->class = $class;
    }
    public function fetch_class()
    {
        return $this->class;
    }
    public function set_method($method)
    {
        $this->method = $method;
    }

    public function fetch_method()
    {
        if ($this->method == $this->fetch_class())
        {
            return $this->routes['index_method'];
        }

        return $this->method;
    }
    public function set_directory($dir)
    {
        $this->directory = $dir.'';
    }
    public function fetch_directory()
    {
        return $this->directory;
    }

}
?>
