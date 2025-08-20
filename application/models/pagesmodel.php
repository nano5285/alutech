<?php
class Pagesmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}	
		
	/**
	 * Get page by slug
	 *
	 * If page slug is not provided, get the root/home page of the current language
	 */
	function get_page($data = '') {
		if (!$data) {
			$slug = $this->uri->segment(1);
			if (!$slug) {
				$slug = '/';
			}
		} else if ($data AND is_numeric($data)) {
			$slug = $this->uri->segment($data);
		} else {
			$slug = $data;
		}
		
		// Return result
		return $this->db->get_where(POSTS_DB_TABLE, array('slug'=>$slug, 'lang'=>$this->config->item('language_abbr'), 'module' => PAGES_M), 1)->row();
	}
}
?>