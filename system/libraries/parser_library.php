<?php defined('SYS') or exit('Access Denied!');
class Parser {

	var $l_delim = '{';
	var $r_delim = '}';
	var $object;
	function parse($template, $data, $return = TRUE)
	{
		loader::sys_helper('view');
		$output = common::register('Output');
		$template = view($template, $data, TRUE);
		
		if ($template == '')
		{
			return FALSE;
		}
		
		foreach ($data as $key => $val)
		{
			if (is_array($val))
			{
				$template = $this->_parse_pair($key, $val, $template);		
			}
			else
			{
				$template = $this->_parse_single($key, (string)$val, $template);
			}
		}
		
		if ($return == FALSE)
		{
			$output->append_output($template);
		}
		
		return $template;
	}
	function set_delimiters($l = '{', $r = '}')
	{
		$this->l_delim = $l;
		$this->r_delim = $r;
	}
	function _parse_single($key, $val, $string)
	{
		return str_replace($this->l_delim.$key.$this->r_delim, $val, $string);
	}
	function _parse_pair($variable, $data, $string)
	{	
		if (FALSE === ($match = $this->_match_pair($string, $variable)))
		{
			return $string;
		}

		$str = '';
		foreach ($data as $row)
		{
			$temp = $match['1'];
			foreach ($row as $key => $val)
			{
				if ( ! is_array($val))
				{
					$temp = $this->_parse_single($key, $val, $temp);
				}
				else
				{
					$temp = $this->_parse_pair($key, $val, $temp);
				}
			}
			
			$str .= $temp;
		}
		
		return str_replace($match['0'], $str, $string);
	}
	function _match_pair($string, $variable)
	{
		if ( ! preg_match("|".$this->l_delim . $variable . $this->r_delim."(.+?)".$this->l_delim . '/' . $variable . $this->r_delim."|s", $string, $match))
		{
			return FALSE;
		}
		
		return $match;
	}

}
?>
