<?php
class Adminmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}
	
	function save($id = FALSE) {
		$id = $this->uri->segment(4);
		$data = array(
					  'identifier' => $this->input->post('identifier'),
					  'title' => $this->input->post('title'),
					  'lang' => $this->input->post('lang'),
					  );
		$this->db->set($data);

		if ($id == FALSE) {
			// if url id does not exist, insert new menu
			$this->db->insert(MENUS_DB_TABLE);

			// return insert id
			return $this->db->insert_id();
		} else {
			// else update the current menu
			$this->db->where('id', $id)->update(MENUS_DB_TABLE);

			// return current id
			return $id;
		}
	}
	
}
?>