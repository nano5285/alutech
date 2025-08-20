<?php
class Extramodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}
	
	function restrict_menu() {		
		echo 'style="display:none"';
	}
	
	function pagination_base_link($categories = FALSE) {
		$seg1 = $this->uri->segment(1);
		$seg2 = $this->uri->segment(2);
		
		$current_language = $this->config->item('language_abbr');
		$default_language = $this->config->item('default_language');
		
		$url = ($categories == TRUE) ? $seg1."/".$seg2."/" : "/".$seg1."/";
		
		if ($current_language == $default_language) {
			return base_url() . $url;
		} else {
			return base_url() . $current_language."/".$url;
		}
	}
}
?>