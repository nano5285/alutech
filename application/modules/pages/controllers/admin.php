<?php

class Admin extends CI_Controller
{
	function __construct() {
		parent::__construct();

		// Redirect if pages module is disabled
		if(!in_array('pages', $this->config->item('cms_modules'))) {
			redirect($this->config->item('base_url') . ADMIN_URL);
			exit();
		}

		$this->lang->load('english', 'english');
		$this->template->set('controller', $this);
		$this->config->load('pages/config');
		$this->auth->restrict(3); // Restrict access to admin and above
		$this->load->model('commonmodel');
		$this->load->model('adminmodel');
		$this->load->helper('admin');
		$this->load->library('form_validation');

		$this->output->enable_profiler($this->config->item('enable_profiler'));
	}

	// Field validation
	private $rules = array(
						array(
							'field'   => 'lang',
							'label'   => 'lang:language',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'title',
							'label'   => 'lang:title',
							'rules'   => 'required',
						),
						array(
							'field'   => 'content',
							'label'   => 'lang:content',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'slug',
							'label'   => 'lang:url',
							'rules'   => 'required|callback__check_slug',
						),
						array(
							'field'   => 'seo_title',
							'label'   => 'lang:seo_h1_title',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'meta_keywords',
							'label'   => 'lang:meta_keywords',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'meta_description',
							'label'   => 'lang:meta_description',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'template',
							'label'   => 'lang:template',
							'rules'   => 'trim',
						),
					);

	/**
	 * List pages
	 */		
	function index($num = '') {
		$this->load->library('pagination');

		// Get filter query strings
		$search_query = $this->input->get('q');
		$search_lang = $this->input->get('lang');

		/**
		 * If search is activated (keyword or search language is defined) get all posts that match the search criteria
		 * If search is not activated, get default list of pages
		 */	
		if (!empty($search_query) OR !empty($search_lang) ) {
			$query = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'module' => PAGES_M, 'search_query' => $search_query, 'lang' => $search_lang));
		} else {			
			$config['per_page'] = $this->config->item('admin_items_per_page');
			$query = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'module' => PAGES_M, 'limit' => $config['per_page'], 'offset' => $num));

			// Configure and initialize pagination
			$config['base_url'] = $this->config->item('base_url') . ADMIN_URL . '/pages';
			$config['total_rows'] = $query['num_rows'];
			$config['prev_link'] = '<i class="fa fa-angle-left"></i>';
			$config['next_link'] = '<i class="fa fa-angle-right"></i>';
			$config['first_link'] = '<i class="fa fa-angle-double-left"></i>';
			$config['last_link'] = '<i class="fa fa-angle-double-right"></i>';

			$this->pagination->initialize($config);
		}

		$data = array(
			'query' => $query['query']->result(),
			'total' => $query['num_rows'],
		);

		$this->template->load_partial('admin/master', 'admin/pages/index', $data);
	}

	/**
	 * Create a new page
	 *
	 * First, validation rules are set for the page fields
	 * and custom page fields (if custom fields are defined)
	 *
	 * If the form does not validate or if it is blank (opened for the first time)
	 * load the admin template with empty form or repopulated values
	 *
	 * If passed validation, save the page and redirect to proper URL
	 */		
	function create() {			
		$this->form_validation->set_rules($this->rules);

		// Validation rules for custom page fields
		$custom_fields = $this->config->item('pages_custom_fields');
		if ($custom_fields) {
			foreach ($custom_fields as $field => $value) {
				$this->form_validation->set_rules('custom_field['.$field.'][content]', '', 'trim');
			}
		}

		if ($this->form_validation->run() == FALSE) {	
			$this->load->helper('wysiwyg');
			$data['query'] = false;
			$this->template->load_partial('admin/master', 'admin/pages/form', $data);
		} else {
			$insert_id = $this->adminmodel->save();
			$this->session->set_flashdata('info', $this->lang->line('success_insert'));
			admin_redirect('pages', $insert_id);
		}
	}

	/**
	 * Edit page
	 *
	 * First, validation rules are set for the page fields
	 * and custom page fields (if custom fields are defined)
	 *
	 * If the page is not saved (save button is not pressed),
	 * get the page data form database and populate the template
	 *
	 * If page is saved (save button is pressed), run the validation.
	 * If the form does not validate, reload the admin template with repopulated values
	 *
	 * If passed validation, save the page and redirect to proper URL
	 */		
	function edit($id) {		
		// Load wysiwyg editor
		$this->load->helper('wysiwyg');

		$this->form_validation->set_rules($this->rules);

		// Validation rules for custom page fields
		$custom_fields = $this->config->item('pages_custom_fields');
		if ($custom_fields) {
			foreach ($custom_fields as $field => $value) {
				$this->form_validation->set_rules('custom_field['.$field.'][id]', '', 'trim');
				$this->form_validation->set_rules('custom_field['.$field.'][identifier]', '', 'trim');
				$this->form_validation->set_rules('custom_field['.$field.'][content]', '', 'trim');
			}
		}

		if (!$this->input->post('save')) {
			$data['query'] = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'id' => $id));
			$data['query'] = $data['query']['query']->row();
			$this->template->load_partial('admin/master', 'admin/pages/form', $data);
		} else {
			if ($this->form_validation->run() == FALSE) {
				// Clear the query and use repopulated form values
				$data['query'] = false;
				$this->template->load_partial('admin/master', 'admin/pages/form', $data);
			} else {
				$id = $this->adminmodel->save();
				$this->session->set_flashdata('info', $this->lang->line('success_update'));
			
				// Clear cache files if auto clear is enabled
				if($this->config->item('cache_auto_clear')) {
					$this->output->clear_all_cache();
				}
				
				admin_redirect('pages', $id);
			}
		}			
	}

	/**
	 * Delete page
	 *
	 * Delete the page and all associated custom fields from the database
	 * and redirect to the index (page list) template
	 *
	 */		
	function delete($id) {
		$this->commonmodel->delete(array('table' => POSTS_DB_TABLE, 'id' => $id, 'module' => PAGES_M));	
		$this->commonmodel->delete(array('table' => POST_META_DB_TABLE, 'post_id' => $id, 'module' => PAGES_M));
		$this->session->set_flashdata('info', $this->lang->line('success_delete'));
		admin_redirect('pages', $id);
	}
	
	/**
	 * Page Slug (URL) validation callback
	 *
	 * Will check if the page URL already exists and return error if it does
	 *
	 */		
	function _check_slug($str) {	
		$slug = trim($str);
		$id = $this->uri->segment(4);
		
		$total_rows = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'module' => PAGES_M, 'slug' => $slug, 'lang' => $this->input->post('lang')));
		$total_rows = $total_rows['num_rows'];

		// When creating a new item, check if slug already exist
		if (!$id) {
			if ($total_rows == 0) {
				return true;
			} else {
				$this->form_validation->set_message('_check_slug', $this->lang->line('slug_error'));
				return false;
			} 
		} else {
			// On update, check if new slug is entered and if already exists
			if ($total_rows == 0) {
				return true;
			} else {
				// Check if slug is the same as the current item slug
				$q = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'id' => $id));
				if ($slug == $q['query']->row()->slug) {
					return true;
				} elseif ($total_rows >= 1) {
					$this->form_validation->set_message('_check_slug', $this->lang->line('slug_error'));
					return false;
				}
			}		
		}
	}

}