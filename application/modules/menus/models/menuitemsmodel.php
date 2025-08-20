<?php
class Menuitemsmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}
	
	function save($items) {	
		// save current menu items
		foreach($items as $item => $value) {
			$item_id = $items[$item]['id'];

			if (isset($items[$item]['delete'])) {
				// if delete checkbox is checked, delete that item
				$this->db->where('id', $item_id)->delete(MENU_ITEMS_DB_TABLE);

				// if submenus exist, remove the parent menu item id and change the status
				$this->db->where('parent', $item_id)->update(MENU_ITEMS_DB_TABLE, array('parent' => 0));
			} else {
				// prepare menu items data for update
				$data = array(
								'title' => $items[$item]['title'],
								'url' => $items[$item]['url'],
								'status' => (isset($items[$item]['status'])) ? $items[$item]['status'] : 0,
								'target' => (isset($items[$item]['target'])) ? $items[$item]['target'] : 0,
							 );

				// set data and update the db
				$this->db->set($data);
				$this->db->where('id', $item_id)->update(MENU_ITEMS_DB_TABLE);
			}
		}
		// save new item if defined
		if($this->input->post('title') AND $this->input->post('url')) {
			$menu_id = $this->uri->segment(3);
			
			// get highest menu item position and increase it for new item
			$item_position = $this->db->where('menu_id', $menu_id)->select_max('position')->get(MENU_ITEMS_DB_TABLE)->row()->position + 1;

			// get new values
			$menu_data = array(
								'title' => $this->input->post('title'),
								'menu_id' => $menu_id,
								'position' => $item_position,
								'url' => $this->input->post('url'),
								'status' => $this->input->post('status'),
								'target' => $this->input->post('target'),
							  );
			
			// insert new item
			$this->db->insert(MENU_ITEMS_DB_TABLE, $menu_data);				
		}				
	}
}
?>