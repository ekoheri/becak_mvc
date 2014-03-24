<?php defined('SYS') or exit('Access Denied!');
class Uri
{
    public $keyval      = array();
    public $uri_string;
    public $segments    = array();
    public $rsegments   = array();
    
    public function __construct(){
        //log_message('debug', "URI Class Initialized");
    }
    public function _fetch_uri_string()
    {
        if (strtoupper(common::config_item('uri_protocol')) == 'AUTO')
        {
            if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '')
            {
                $this->uri_string = key($_GET);
                return;
            }
            $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');            
            if (trim($path, '/') != '' && $path != "/".SELF)
            {
                $this->uri_string = $path;
                return;
            }
            $path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');    
            if (trim($path, '/') != '')
            {
                $this->uri_string = $path;
                return;
            }
            $path = (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO');    
            if (trim($path, '/') != '' && $path != "/".SELF)
            {
                $this->uri_string = str_replace($_SERVER['SCRIPT_NAME'], '', $path);
                return;
            }
            $this->uri_string = '';
        }
        else
        {
            $uri = strtoupper(config_item('uri_protocol'));
            
            if ($uri == 'REQUEST_URI')
            {
                $this->uri_string = $this->_parse_request_uri();
                return;
            }
            
            $this->uri_string = (isset($_SERVER[$uri])) ? $_SERVER[$uri] : @getenv($uri);
        }
        
        if ($this->uri_string == '/')
        {
            $this->uri_string = '';
        }        
    }

    public function _parse_request_uri()
    {
        if ( ! isset($_SERVER['REQUEST_URI']) OR $_SERVER['REQUEST_URI'] == '')
        {
            return '';
        }
        
        $request_uri = preg_replace("|/(.*)|", "\\1", str_replace("\\", "/", $_SERVER['REQUEST_URI']));

        if ($request_uri == '' OR $request_uri == SELF)
        {
            return '';
        }
        
        $fc_path = FCPATH.SELF;        
        if (strpos($request_uri, '?') !== FALSE)
        {
            $fc_path .= '?';
        }
        
        $parsed_uri = explode("/", $request_uri);
                
        $i = 0;
        foreach(explode("/", $fc_path) as $segment)
        {
            if (isset($parsed_uri[$i]) && $segment == $parsed_uri[$i])
            {
                $i++;
            }
        }
        
        $parsed_uri = implode("/", array_slice($parsed_uri, $i));
        
        if ($parsed_uri != '')
        {
            $parsed_uri = '/'.$parsed_uri;
        }

        return $parsed_uri;
    }

    public function _filter_uri($str)
    {
        if ($str != '' && common::config_item('permitted_uri_chars') != '' && common::config_item('enable_query_strings') == FALSE)
        {
            if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote(common::config_item('permitted_uri_chars'), '-'))."]+$|i", $str))
            {
                show_error('The URI you submitted has disallowed characters.', 400);
            }
        }
        $bad    = array('$',         '(',         ')',       '%28',    '%29');
        $good   = array('&#36;',    '&#40;',    '&#41;',    '&#40;',   '&#41;');

        return str_replace($bad, $good, $str);
    }
    public function _remove_url_suffix()
    {
        if  (common::config_item('url_suffix') != "")
        {
            $this->uri_string = preg_replace("|".preg_quote(config_item('url_suffix'))."$|", "", $this->uri_string);
        }
    }
    public function _explode_segments()
    {
        foreach(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val)
        {
             $val = trim($this->_filter_uri($val));
            
            if ($val != '')
            {
                $this->segments[] = $val;
            }
        }
    }
    
    public function _reindex_segments()
    {
        array_unshift($this->segments, NULL);
        array_unshift($this->rsegments, NULL);
        unset($this->segments[0]);
        unset($this->rsegments[0]);
    }    
    public function segment($n, $no_result = FALSE)
    {
        return ( ! isset($this->segments[$n])) ? $no_result : $this->segments[$n];
    }
    public function rsegment($n, $no_result = FALSE)
    {
        return ( ! isset($this->rsegments[$n])) ? $no_result : $this->rsegments[$n];
    }
    public function uri_to_assoc($n = 3, $default = array())
    {
         return $this->_uri_to_assoc($n, $default, 'segment');
    }
    public function ruri_to_assoc($n = 3, $default = array())
    {
         return $this->_uri_to_assoc($n, $default, 'rsegment');
    }
    public function _uri_to_assoc($n = 3, $default = array(), $which = 'segment')
    {
        if ($which == 'segment')
        {
            $total_segments = 'total_segments';
            $segment_array = 'segment_array';
        }
        else
        {
            $total_segments = 'total_rsegments';
            $segment_array = 'rsegment_array';
        }
        
        if ( ! is_numeric($n))
        {
            return $default;
        }
    
        if (isset($this->keyval[$n]))
        {
            return $this->keyval[$n];
        }
    
        if ($this->$total_segments() < $n)
        {
            if (count($default) == 0)
            {
                return array();
            }
            
            $retval = array();
            foreach ($default as $val)
            {
                $retval[$val] = FALSE;
            }        
            return $retval;
        }

        $segments = array_slice($this->$segment_array(), ($n - 1));

        $i = 0;
        $lastval = '';
        $retval  = array();
        foreach ($segments as $seg)
        {
            if ($i % 2)
            {
                $retval[$lastval] = $seg;
            }
            else
            {
                $retval[$seg] = FALSE;
                $lastval = $seg;
            }
        
            $i++;
        }

        if (count($default) > 0)
        {
            foreach ($default as $val)
            {
                if ( ! array_key_exists($val, $retval))
                {
                    $retval[$val] = FALSE;
                }
            }
        }
        $this->keyval[$n] = $retval;
        return $retval;
    }
    public function assoc_to_uri($array)
    {    
        $temp = array();
        foreach ((array)$array as $key => $val)
        {
            $temp[] = $key;
            $temp[] = $val;
        }
        
        return implode('/', $temp);
    }
    public function slash_segment($n, $where = 'trailing')
    {
        return $this->_slash_segment($n, $where, 'segment');
    }
    public function slash_rsegment($n, $where = 'trailing')
    {
        return $this->_slash_segment($n, $where, 'rsegment');
    }

    public function _slash_segment($n, $where = 'trailing', $which = 'segment')
    {    
        if ($where == 'trailing')
        {
            $trailing    = '/';
            $leading    = '';
        }
        elseif ($where == 'leading')
        {
            $leading    = '/';
            $trailing    = '';
        }
        else
        {
            $leading    = '/';
            $trailing    = '/';
        }
        return $leading.$this->$which($n).$trailing;
    }

    public function segment_array()
    {
        return $this->segments;
    }

    public function rsegment_array()
    {
        return $this->rsegments;
    }

    public function total_segments()
    {
        return count($this->segments);
    }

    public function total_rsegments()
    {
        return count($this->rsegments);
    }

    public function uri_string()
    {
        return $this->uri_string;
    }


    public function ruri_string()
    {
        return '/'.implode('/', $this->rsegment_array()).'/';
    }


}
?>
