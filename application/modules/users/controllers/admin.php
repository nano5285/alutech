<?php

class Admin extends CI_Controller
{
	function __construct() {
		parent::__construct();
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
							'field'   => 'username',
							'label'   => 'lang:username',
							'rules'   => 'trim|required|valid_email|callback__check_username',
						),
						array(
							'field'   => 'name',
							'label'   => 'lang:name',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'group',
							'label'   => 'lang:user_group',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'bio',
							'label'   => 'lang:bio',
							'rules'   => 'trim',
						),								
					);

	// Password validation rules
	private $password_rules = array(
					array(
						'field'   => 'password',
						'label'   => 'lang:password',
						'rules'   => 'trim|required|matches[password2]|min_length[8]',
					),
					array(
						'field'   => 'password2',
						'label'   => 'lang:confirm_password',
						'rules'   => 'trim|required|matches[password]',
					),
				);							

	/**
	 * List users
	 */		
	function index($num = '') {	
		$this->auth->restrict(3);
		
		$config['per_page'] = $this->config->item('admin_items_per_page');
		$query = $this->adminmodel->get($config['per_page'], $num);

		// Configure and initialize pagination
		$this->load->library('pagination');

		// Configure and initialize pagination
		$config['base_url'] = $this->config->item('base_url') . ADMIN_URL . '/users';
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

		$this->template->load_partial('admin/master', 'admin/users/index', $data);
	}

	/**
	 * Create a new user
	 *
	 * Creating users is restricted only to level 3 (admin) and above user groups
	 *
	 * If the form does not validate or if it is blank (opened for the first time)
	 * load the admin template with empty form or repopulated values
	 *
	 * If passed validation, save the user and redirect to proper URL
	 */	
	function create() {	
		$this->auth->restrict(3);

		$this->form_validation->set_rules($this->rules);
		$this->form_validation->set_rules($this->password_rules);

		if ($this->form_validation->run() == FALSE) {	
			$data = array(
							'query' => false,
							'groups' => $this->adminmodel->get_groups()
						 );
			$this->template->load_partial('admin/master', 'admin/users/form', $data);
		} else {
			$insert_id = $this->adminmodel->save();
			$this->session->set_flashdata('info', $this->lang->line('success_insert'));
			admin_redirect('users', $insert_id);
		}
	}

	/**
	 * Edit user
	 *
	 * Updating users is restricted only to level 3 (admin) and above user groups
	 *
	 * Checking if new password is inserted. If it is, 
	 * check if it matches the one in the confirm password field
	 *
	 * If the user is not saved (save button is not pressed),
	 * get the user data form database and populate the template
	 *
	 * If the user is saved (save button is pressed), run the validation.
	 * If the form does not validate, reload the admin template with repopulated values
	 *
	 * If passed validation, save the user and redirect to proper URL
	 */			
	function edit($id) {
		$this->auth->restrict(3);

		$uid = $this->adminmodel->get_user($id);
		if($uid->group_id <= $this->session->userdata('group_id')) {
			$this->form_validation->set_rules($this->rules);
			if ($this->input->post('password') OR $this->input->post('password2')) {
				$this->form_validation->set_rules($this->password_rules);
			}

			$data['groups'] = $this->adminmodel->get_groups();	
				
			if (!$this->input->post('save')) {
				$query = $this->commonmodel->get(array('table' => USERS_DB_TABLE, 'id' => $id));
				$data['query'] = $query['query']->row();
				$this->template->load_partial('admin/master', 'admin/users/form', $data);
			} else {
				if ($this->form_validation->run() == FALSE) {
					// Clear the query and use repopulated form values
					$data['query'] = false;
					$this->template->load_partial('admin/master', 'admin/users/form', $data);
				} else {
					$this->adminmodel->save();
					$this->session->set_flashdata('info', $this->lang->line('success_update'));			
					admin_redirect('users', $id);
				}
			}
		} else {
			$this->session->set_flashdata('error', $this->lang->line('insufficient_rights'));
			admin_redirect('users');
		}
	}	

	/**
	 * Edit profile
	 *
	 * Edit profile is restricted to level 1 (basic logged in user) and above user groups
	 *
	 * Checking if new password is inserted. If it is, 
	 * check if it matches the one in the confirm password field
	 *
	 * If the form does not validate
	 * load the admin template with empty form or repopulated values
	 *
	 * If passed validation, save the user and redirect to proper URL
	 */		
	function profile() {		
		$this->auth->restrict(1);
		$id = $this->session->userdata('user_id');
		$this->form_validation->set_rules($this->rules);
		
		if ($this->input->post('password') OR $this->input->post('password2')) {
			$this->form_validation->set_rules($this->password_rules);
		}

		$data['groups'] = $this->adminmodel->get_groups();	
		
		if (!$this->input->post('save')) {
			$query = $this->commonmodel->get(array('table' => USERS_DB_TABLE, 'id' => $id));
			$data['query'] = $query['query']->row();
			$this->template->load_partial('admin/master', 'admin/users/form', $data);
		} else {
			if ($this->form_validation->run($this) == FALSE) {
				// Clear the query and use repopulated form values
				$data['query'] = false;
				$this->template->load_partial('admin/master', 'admin/users/form', $data);
			} else {	
				$this->adminmodel->save();
				$this->session->set_flashdata('info', $this->lang->line('success_update'));
				admin_redirect('users/profile', $id);				
			}
		}
	}

	/**
	 * Delete user
	 *
	 * Delete the user from the database and redirect to the index (user list) template
	 *
	 */		
	function delete($id) {
		$this->auth->restrict(3);

		$uid = $this->adminmodel->get_user($id);
		if($uid->group_id <= $this->session->userdata('group_id')) {	
			$total_users = $this->commonmodel->get(array('table' => USERS_DB_TABLE));
			$total_users = $total_users['num_rows'];

			// Prevent deleting the last user or yourself
			if($total_users <= 1 OR $this->session->userdata('user_id') == $id) {
				$this->session->set_flashdata('error', $this->lang->line('user_delete_error'));
			} elseif($this->uri->segment(5)) {
				$this->adminmodel->update_posts($id, $this->uri->segment(5));
				$this->commonmodel->delete(array('table' => USERS_DB_TABLE, 'id' => $id));
				$this->session->set_flashdata('info', $this->lang->line('success_delete'));
			} else {
				$this->commonmodel->delete(array('table' => USERS_DB_TABLE, 'id' => $id));
				$this->session->set_flashdata('info', $this->lang->line('success_delete'));
			}
			admin_redirect('users', $id);
		} else {
			$this->session->set_flashdata('error', $this->lang->line('insufficient_rights'));
			admin_redirect('users');			
		}
	}

	/**
	 * Username validation callback
	 *
	 * This will check if the user with the same user name already exists and return error if it does
	 *
	 */		
	function _check_username($str) {

		/**
		 * If edit profile is opened, set id to current user
		 * Else, if edit user is clicked, use the id of the choosen user
		 */	
		$id = ($this->uri->segment(3) == 'profile') ? $this->session->userdata('user_id') : $this->uri->segment(4);
		
		if ($id == FALSE) {
			// If id is not defined, check if choosen username already exist
			if ($this->auth->username_exists($str) == FALSE) {
				return true;
			} else {
				// Return error if username already exist
				$this->form_validation->set_message('_check_username', $this->lang->line('username_exist'));
				return false;
			}		
		} else if ($id == true || $this->uri->segment(3) == 'profile') {
			// If ID is defined of edit profile is opened
			// Check if username exists
			if ($this->auth->username_exists($str) == FALSE) {
				return true;
			} else {
				// If it does exists, and if it is the same as current user, leave it as is
				$q = $this->commonmodel->get(array('table' => USERS_DB_TABLE, 'id' => $id));
				if ( $str == $q['query']->row()->username ) {
					return true;
				} else {
					// If it exists and if it is different then current one, show error message
					$this->form_validation->set_message('_check_username', $this->lang->line('username_exist'));
					return false;
				}
			}	
		}
	}
}