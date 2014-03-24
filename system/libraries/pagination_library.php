<?php
defined('SYS') or exit('Access Denied!');
class Pagination {
    public $show_number = true;
    public $url_reference = '';
    public $format = '/%#%/';
    public $total = 1;
    public $limit = 5;
    public $current = 0;
    public $show_all = false;
    public $no_href = false;
    public $prev_next = true;
    public $prev_text = '&laquo; Previous';
    public $next_text = 'Next &raquo;';
    public $group_separator = '...';
    public $end_size = 1;
    public $mid_size = 2;
    public function __construct(){
        $this->total    = (int) $this->total;
        $this->limit    = (int) $this->limit;
        $this->current  = (int) $this->current;
        $this->end_size = (int) $this->end_size;
        $this->mid_size = (int) $this->mid_size;
    }
    public function get_url(){
	
        $total    = ceil($this->total / $this->limit);
        if ( $total < 2 )
            return;
        
        $end_size   = (0 < $this->end_size) ? $this->end_size : 1; // Out of bounds?  Make it the default.
        $mid_size   = (0 <= $this->mid_size) ? $this->mid_size : 2;
        
        $r          = '';
        $paging_url = array();
        $n          = 0;
        $dots       = false;
        if ( $this->prev_next && $this->current && 1 < $this->current ) {
           
            $link         = str_replace('%#%', $this->current - 1, $this->base);
            $paging_url[] = ( $this->no_href )? array('link' => $link, 'value' => $this->prev_text) : '<a href="'.$link.'">'.$this->prev_text.'</a>';
            
        }
        
        for ( $n = 1; $n <= $total; $n++ ) {
            
            if ( $n == $this->current ) {
                
                if($this->show_number){
                    
                    $paging_url[] = ( $this->no_href )? array('link' => '', 'value' => $n) : '<span class="current">'.$n.'</span>';
                    $dots = true;
                }
            }
            else {
                
                if ( $this->show_all || ( $n <= $end_size || ( $this->current && $n >= $this->current - $mid_size && $n <= $this->current + $mid_size ) || $n > $total - $end_size ) ) {
                    
                    $link = str_replace('%_%', ($n == 1)? '' : $this->format, $this->base);
                    $link = str_replace('%#%', $n, $link);
                    
                    if($this->show_number){
                        
                        $paging_url[] = ( $this->no_href )? array('link' => $link, 'value' => $n) : '<a href="'.$link.'">'.$n.'</a>';
                        $dots = true;
                    }
                }
                elseif ( $dots && !$this->show_all ) {
                    
                    $paging_url[] = ( $this->no_href )? array('link' => '', 'value' => $this->group_separator) : $this->group_separator;
                    $dots = false;
                }
            }
            
        }
        
        if ( $this->prev_next && $this->current && ( $this->current < $total || -1 == $total ) ) {
            
            $link           = str_replace('%_%', $this->format, $this->base);
            $link           = str_replace('%#%', $this->current + 1, $link);
            
            $paging_url[]   = ( $this->no_href )? array('link' => $link, 'value' => $this->next_text) : '<a href="'.$link.'">'.$this->next_text.'</a>';
        }
        return $paging_url;
    }
    
} // End Pagination Class.
?>
