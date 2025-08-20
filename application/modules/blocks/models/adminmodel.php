<?php
class Adminmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
	 * Insert / update
	 * 
	 * This function will update the current block or insert a new one if it does not already exist
	 * Block ID is retrieved from the URL to check if the block exists or not
	 *
	 */	
	function save() {
		$id = $this->uri->segment(4);
		
		$data = array(
					  'identifier' => $this->input->post('identifier'),
					  'title' => $this->input->post('title'),
					  'content' => $this->input->post('content'),
					  'editor' => $this->input->post('use_editor'),
					  'lang' => $this->input->post('lang')
					  );

		$this->db->set($data);

		/**
		 * If block ID does not exist (not provided in the URL), insert new block.
		 * Else if block ID does exist, just update the current block. 
		 */		
		if (!$id) {
			$this->db->insert(POST_META_DB_TABLE);

			return $this->db->insert_id();
		} else {
			$this->db->where('id', $id)->update(POST_META_DB_TABLE);

			return $id;
		}
	}

}
?>