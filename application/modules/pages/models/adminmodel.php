<?php
class Adminmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
	 * Insert / update
	 * 
	 * This function will update the current page or insert a new one if it does not already exist
	 * Page ID is retrieved from the URL to check if the page exists or not
	 */	
	function save() {
		$date = date('Y-m-d H:i:s');
		$id = $this->uri->segment(4);
		
		$data = array(
					'title' => $this->input->post('title'),
					'content' => $this->input->post('content'),
					'seo_title' => $this->input->post('seo_title'),
					'slug' => $this->input->post('slug'),
					'meta_keywords' => ($this->input->post('meta_keywords')) ? $this->input->post('meta_keywords') : '',
					'meta_description' => $this->input->post('meta_description'),
					'lang' => $this->input->post('lang'),
					'tpl' => $this->input->post('template'),
					'modified' => $date,
					);

		/**
		 * If page ID does not exist (not provided in the URL), insert new page.
		 * Else if page ID does exist, just update the current page. 
		 */
		if (!$id) {	
			$data['module'] = PAGES_M;
			$data['status'] = 1;
			$data['author'] = $this->session->userdata('user_id');
			$data['date'] = $date;

			$this->db->insert(POSTS_DB_TABLE, $data);

			// Get the ID of the inserted page
			$id = $this->db->insert_id();		
		} else {
			$this->db->set($data);
			$this->db->where('id', $id)->update(POSTS_DB_TABLE);
		}

		// Processing custom fields
		$this->commonmodel->save_post_meta(array('post_id' => $id, 'module' => PAGES_M));

		return $id;
	}

}
?>