<?php

class Admin extends CI_Controller
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
		$this->load->model('commonmodel');
		$this->load->model('adminmodel');
		$this->load->helper('admin');
		$this->load->library('form_validation');	

		$this->output->enable_profiler($this->config->item('enable_profiler'));		
	}	

	// Field validation
	private $rules = array(
							array(
								'field'   => 'title',
								'label'   => 'lang:title',
								'rules'   => 'trim',
							),
							array(
								'field'   => 'lang',
								'label'   => 'lang:language',
								'rules'   => 'trim',
							),
							array(
								'field'   => 'identifier',
								'label'   => 'lang:identifier',
								'rules'   => 'trim|required',
							),
							array(
								'field'   => 'text',
								'label'   => 'lang:title',
								'rules'   => 'trim',
							),
						);		

	/**
	 * List menus
	 */		
	function index($num = '') {
		$this->auth->restrict(3);
		$this->load->library('pagination');

		$config['per_page'] = $this->config->item('admin_items_per_page');
		$query = $this->commonmodel->get(array('table' => MENUS_DB_TABLE, 'limit' => $config['per_page'], 'offset' => $num));

		$config['base_url'] = $this->config->item('base_url') . ADMIN_URL . '/menus';
		$config['total_rows'] = $query['num_rows'];
		$config['prev_link'] = '<i class="fa fa-angle-left"></i>';
		$config['next_link'] = '<i class="fa fa-angle-right"></i>';
		$config['first_link'] = '<i class="fa fa-angle-double-left"></i>';
		$config['last_link'] = '<i class="fa fa-angle-double-right"></i>';

		$this->pagination->initialize($config);

		$data = array(
			'query' => $query['query']->result(),
			'total' => $query['num_rows'],
		);

		$this->template->load_partial('admin/master', 'admin/menus/index', $data);
	}

	/**
	 * Create Menu
	 */		
	function create() {	
		$this->auth->restrict(4);

		$this->form_validation->set_rules($this->rules);

		if ($this->form_validation->run() == FALSE) {	
			$data['query'] = false;
			$this->template->load_partial('admin/master', 'admin/menus/menu_form', $data);
		} else {
			$insert_id = $this->adminmodel->save();
			$this->session->set_flashdata('info', $this->lang->line('success_insert'));
			
			admin_redirect('menus', $insert_id);
		}
	}	

	/**
	 * Edit Menu
	 */		
	function edit($id) {
		$this->auth->restrict(4);

		$this->form_validation->set_rules($this->rules);
		
		if (!$this->input->post('save')) {
			$q = $this->commonmodel->get(array('table' => MENUS_DB_TABLE, 'id' => $id));
			$data['query'] = $q['query']->row();
			$this->template->load_partial('admin/master', 'admin/menus/menu_form', $data);
		} else {
			if ($this->form_validation->run() == FALSE) {
				$data['query'] = false;
				$this->template->load_partial('admin/master', 'admin/menus/menu_form', $data);
			} else {
				$this->adminmodel->save();
				$this->session->set_flashdata('info', $this->lang->line('success_update'));
				
				admin_redirect('menus', $id);
			}
		}			
	}	
	
	/**
	 * Delete Menu
	 */	
	function delete($id) {
		$this->auth->restrict(4);
		$this->commonmodel->delete(array('table' => MENUS_DB_TABLE, 'id' => $id));
		$this->commonmodel->delete(array('table' => MENU_ITEMS_DB_TABLE, 'menu_id' => $id));
		$this->session->set_flashdata('info', $this->lang->line('success_delete'));
		admin_redirect('menus', $id);
	}		
}