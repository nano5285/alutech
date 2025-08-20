<?php

class Admin extends CI_Controller
{
	function __construct() {
		parent::__construct();
		$this->lang->load('english', 'english');
		$this->template->set('controller', $this);
		$this->load->helper('admin');
		$this->load->library('form_validation');
	}
	
	private $rules = array(
						array(
							'field'   => 'username',
							'label'   => 'lang:username',
							'rules'   => 'trim|required|valid_email',
						),
						array(
							'field'   => 'password',
							'label'   => 'lang:password',
							'rules'   => 'trim|required|min_length[8]',
						)
					);
	
	/**
	 * If already logged in, redirect to admin dashboard
	 */
	function index() {
		$this->load->model('commonmodel');
		if ($this->auth->logged_in()) {
			$data['query'] = $this->db->order_by('modified', 'desc')->get(POSTS_DB_TABLE, 10)->result();

			$this->template->load_partial('admin/master', 'admin/dashboard', $data);
		} else {
			$this->login();
		}
	}

	/**
	 * User Login
	 *
	 * If form validation has failed, load login form view
	 * Else try to login with provided username and password
	 */
	function login() {
		$data['error'] = FALSE;		
		$this->form_validation->set_rules($this->rules);

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('admin/login', $data);
		} else {
			$this->auth->login($this->input->post('username'), $this->input->post('password'), $this->config->item('base_url') . ADMIN_URL, 'admin/login');
		}		
	}

	/**
	 * Logout and redirect back to login form
	 */		
	function logout() {
		$this->auth->logout($this->config->item('base_url') . ADMIN_URL);	
	}
}

?>