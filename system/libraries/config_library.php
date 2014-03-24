<?php defined('SYS') or exit('Access Denied!');
class Config {
    public $config    = array();
    public $is_loaded = array();

    public function __construct()
    {
        $this->config = common::get_config();
    }
      
    public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
    {
        $file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
    
        if (in_array($file, $this->is_loaded, TRUE))
        {
            return TRUE;
        }

        if ( ! file_exists(APP .'config'. DS .$file. EXT))
        {
            if ($fail_gracefully === TRUE)
            {
                return FALSE;
            }
            
            throw new Exception('The configuration file '.$file. '.PHP does not exist.');
        }
    
        include(APP .'config'. DS .$file. EXT);

        if ( ! isset($config) OR ! is_array($config))
        {
            if ($fail_gracefully === TRUE)
            {
                return FALSE;
            }
            
            throw new Exception('Your '.$file. '.PHP file does not appear to contain a valid configuration array.');
        }

        if ($use_sections === TRUE)
        {
            if (isset($this->config[$file]))
            {
                $this->config[$file] = array_merge($this->config[$file], $config);
            }
            else
            {
                $this->config[$file] = $config;
            }
        }
        else
        {
            $this->config = array_merge($this->config, $config);
        }

        $this->is_loaded[] = $file;
        unset($config);

        //log_message('debug', 'Config file loaded: config/'.$file.EXT);
        return TRUE;
    }
      
    public function item($item, $index = '')
    {    
        if ($index == '')
        {    
            if ( ! isset($this->config[$item]))
            {
                return FALSE;
            }

            $pref = $this->config[$item];
        }
        else
        {
            if ( ! isset($this->config[$index]))
            {
                return FALSE;
            }

            if ( ! isset($this->config[$index][$item]))
            {
                return FALSE;
            }

            $pref = $this->config[$index][$item];
        }

        return $pref;
    }
    public function slash_item($item)
    {
        if ( ! isset($this->config[$item]))
        {
            return FALSE;
        }

        $pref = $this->config[$item];

        if ($pref != '' && substr($pref, -1) != '/')
        {    
            $pref .= '/';
        }

        return $pref;
    }
    public function site_url($uri = '')
    {
        if (is_array($uri))
        {
            $uri = implode('/', $uri);
        }

        if ($uri == '')
        {
            return $this->slash_item('base_url').$this->item('index_page');
        }
        else
        {
            $suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
            return $this->slash_item('base_url').$this->slash_item('index_page').preg_replace("|^/*(.+?)/*$|", "\\1", $uri).$suffix;
        }
    }
    public function set_item($item, $value)
    {
        $this->config[$item] = $value;
    }

}

?>
