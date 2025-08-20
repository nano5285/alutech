<?php

class Menuitems extends CI_Controller
{
	function __construct() {
		parent::__construct();
		
		// Redirect if module is disabled
		if(!in_array('menus', $this->config->item('cms_modules'))) {
			redirect($this->config->item('base_url') . ADMIN_URL);
			exit();
		}

		$this->lang->load('english', 'english');
		$this->template->set('controller', $this);
		$this->auth->restrict(3); //restrict access to admin and above
		$this->load->model('commonmodel');	
		$this->load->model('menuitemsmodel');
		$this->load->helper('admin');
		$this->load->library('form_validation');
		
		$this->output->enable_profiler($this->config->item('enable_profiler'));
	}

	// Field validation
	private $rules = array(
						array(
							'field'   => 'title',
							'label'   => 'lang:title',
							'rules'   => 'callback__validate_item_url',
						),
						array(
							'field'   => 'url',
							'label'   => 'lang:url',
							'rules'   => 'callback__validate_item_title',
						),
					);
	
	/**
	 * List menu items
	 */		
	function index($id) {
		$this->form_validation->set_rules($this->rules);

		if ($this->form_validation->run() == FALSE)  {
			$q = $this->commonmodel->get(array('table' => MENU_ITEMS_DB_TABLE, 'menu_id' => $id, 'sort_column' => 'position', 'sort_direction' => 'asc'));
			$data['query'] = $q['query']->result();
			$this->template->load_partial('admin/master', 'admin/menus/index_items', $data);
		} else {
			$menu_items = $this->input->post('menu_item');
			$this->menuitemsmodel->save($menu_items);

			$this->session->set_flashdata('info', $this->lang->line('success_update'));	
				
			// Clear cache files if cache is enabled
			if($this->config->item('cache_auto_clear')) {
				$this->output->clear_all_cache();
			}
			
			admin_redirect('menuitems/'.$id);
		}
	}

	/**
	 * Reorder menu items
	 */		
	function reorder($id) {
		$data['query'] = '';
		$this->template->load_partial('admin/master', 'admin/menus/reorder', $data);
	}
	
	/**
	 * Ajax - reorder menu items
	 */	
	function reorder_menuitems() {
		$items = $this->input->post('items');
		$menu_id = $this->input->post('menu_id');

		$i = 0;
		foreach ($items as $item) {
			$id = $item["item_id"];
			$data["position"] = $i;
			$parent = $item['parent_id'];
			$parent = ($parent == "root") ? $data["parent"] = 0 : $data["parent"] = $parent;

			$this->commonmodel->update(array('table' => MENU_ITEMS_DB_TABLE, 'id' => $id), $data);
			$i++;
		}

		// Clear cache files if cache is enabled
		if($this->config->item('cache_auto_clear')) {
			$this->output->clear_all_cache();
		}
	}

	/**
	 * Menu Item URL validation
	 */	
	function _validate_item_url($str) {
		$item_title = $this->input->post('title');
		$item_url = $this->input->post('url');

		// If menu item title is defined and the item url is not, show error message...
		if(!empty($item_title) && empty($item_url)) {
			$this->form_validation->set_message('_validate_item_url', $this->lang->line('enter_menu_url'));
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Menu Item title validation
	 */	
	function _validate_item_title($str) {
		$item_title = $this->input->post('title');
		$item_url = $this->input->post('url');

		// If menu item url is defined and the item title is not, show error message...
		if(empty($item_title) && !empty($item_url)) {
			$this->form_validation->set_message('_validate_item_title', $this->lang->line('enter_menu_title'));
			return false;
		} else {
			return true;
		}
	}

}