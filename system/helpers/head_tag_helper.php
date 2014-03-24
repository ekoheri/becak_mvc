<?php defined('SYS') or exit('Access Denied!');
function css($filename, $path = 'css', $title = '', $media = '')
{
    if( ! is_array($filename))
    $filename = array($filename);
    
    /*$_cont = base::getInstance();
    
    if(isset($_cont->_ent->css_folder{1}))
    {
        $path = $path . $_cont->_ent->css_folder; 
    }*/

    $url = base::getInstance()->config->slash_item('source_url').'css';
    
    $style = '';
    foreach($filename as $key => $css)
    {    
        $style .= "\n".link_tag($url.'/'.$css, 'stylesheet', 'text/css', $title, $media);
    }
    
    return $style;   
}
function js($filename, $arguments = '', $type = 'text/javascript')
{
    if( ! is_array($filename))
    $filename = array($filename);
    
    $url = base::getInstance()->config->slash_item('source_url').'js/';

    $js = '';
    foreach($filename as $key => $file)
    {
        $js.= "\n".'<script type="'.$type.'" src="'.$url.$file.'" '.$arguments.'></script>';  
    }
    
    return $js;
}
function meta($name = '', $content = '', $type = 'name', $newline = "\n")
{
    if ( ! is_array($name))
    {
        $name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));
    }
    else
    {
        // Turn single array into multidimensional
        if (isset($name['name']))
        {
            $name = array($name);
        }
    }

    $str = '';
    foreach ($name as $meta)
    {
        $type       = ( ! isset($meta['type']) OR $meta['type'] == 'name') ? 'name' : 'http-equiv';
        $name       = ( ! isset($meta['name']))     ? ''     : $meta['name'];
        $content    = ( ! isset($meta['content']))    ? ''     : $meta['content'];
        $newline    = ( ! isset($meta['newline']))    ? "\n"    : $meta['newline'];

        $str .= '<meta '.$type.'="'.$name.'" content="'.$content.'" />'.$newline;
    }

    return $str;
}
function link_tag($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = FALSE)
{
    $OB = base::getInstance();

    $link = '<link ';

    if (is_array($href))
    {
        foreach ($href as $k=>$v)
        {
            if ($k == 'href' AND strpos($v, '://') === FALSE)
            {
                if ($index_page === TRUE)
                {
                    $link .= ' href="'.$OB->config->site_url($v).'" ';
                }
                else
                {
                    $link .= ' href="'.$OB->config->slash_item('base_url').$v.'" ';
                }
            }
            else
            {
                $link .= "$k=\"$v\" ";
            }
        }

        $link .= "/>";
    }
    else
    {
        if ( strpos($href, '://') !== FALSE)
        {
            $link .= ' href="'.$href.'" ';
        }
        elseif ($index_page === TRUE)
        {
            $link .= ' href="'.$OB->config->site_url($href).'" ';
        }
        else
        {
            $link .= ' href="'.$OB->config->slash_item('base_url').$href.'" ';
        }

        $link .= 'rel="'.$rel.'" type="'.$type.'" ';

        if ($media    != '')
        {
            $link .= 'media="'.$media.'" ';
        }

        if ($title    != '')
        {
            $link .= 'title="'.$title.'" ';
        }

        $link .= '/>';
    }
    
    return $link;
}
?>
