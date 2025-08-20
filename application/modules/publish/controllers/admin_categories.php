<?php

class Admin_categories extends CI_Controller
{
	function __construct() {
		parent::__construct();

		// Redirect if module is disabled
		if(!in_array('publish', $this->config->item('cms_modules'))) {
			redirect($this->config->item('base_url') . ADMIN_URL);
			exit();
		}

		$this->lang->load('english', 'english');
		$this->template->set('controller', $this);
		$this->config->load('publish/config');
		$this->load->model('commonmodel');
		$this->load->model('adminmodel');
		$this->load->helper('admin');
		$this->load->library('form_validation');
		
		$this->output->enable_profiler($this->config->item('enable_profiler'));
	}

	// Validation rules
	private $rules = array(
		array(
			'field'   => 'lang',
			'label'   => 'lang:language',
			'rules'   => 'trim|required',
		),
		array(
			'field'   => 'position',
			'label'   => 'lang:position',
			'rules'   => 'trim|required',
		),
		array(
			'field'   => 'title',
			'label'   => 'lang:title',
			'rules'   => 'trim|required',
		),
		array(
			'field'   => 'template',
			'label'   => 'lang:template',
			'rules'   => 'trim',
		),
		array(
			'field'   => 'slug',
			'label'   => 'lang:url',
			'rules'   => 'required|callback__check_slug',
		),
		array(
			'field'   => 'date',
			'label'   => 'lang:publish_date',
			'rules'   => 'trim|required',
		),
		array(
			'field'   => 'status',
			'label'   => 'lang:published',
			'rules'   => 'trim',
		),
		array(
			'field'   => 'content',
			'label'   => 'lang:content',
			'rules'   => 'trim',
		),
		array(
			'field'   => 'image_title',
			'label'   => '',
			'rules'   => 'trim',
		),
		array(
			'field'   => 'image_alt',
			'label'   => '',
			'rules'   => 'trim',
		),
		array(
			'field'   => 'seo_title',
			'label'   => 'lang:seo_title',
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
	);

	/**
	 * List Categories
	 */
	function index($num = '') {
		$this->auth->restrict(2);
		$this->load->library('pagination');

		// Filter query strings
		$search_query = $this->input->get('q');
		$search_lang = $this->input->get('lang');

		/**
		 * Get all or filtered categories
		 */
		if ( !empty($search_query) OR !empty($search_lang) ) {
			$query = $this->adminmodel->categories(array('search_query' => $search_query, 'lang' => $search_lang));
		} else {		
			$config['per_page'] = $this->config->item('admin_items_per_page');
			$query = $this->adminmodel->categories(array('limit' => $config['per_page'], 'offset' => $num));

			// Configure and initialize pagination
			$config['base_url'] = $this->config->item('base_url') . ADMIN_URL . '/publish/category';
			$config['total_rows'] = $query['num_rows'];
			$config['uri_segment'] = 4;
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

		// Load the admin template
		$this->template->load_partial('admin/master', 'admin/publish/categories', $data);
	}
	
	/**
	 * Create a Category
	 */		
	function create() {	
		$this->auth->restrict(2);

		// Validation rules
		$this->form_validation->set_rules($this->rules);
		$this->load->helper('wysiwyg');

		// Save the category if save button is clicked
		if (!$this->input->post('save')) {
			$data = array(
				'query' => false,
				'image_id' => false,
				'image_name' => false,
				'image_title' => false,
				'image_alt' => false,
				'date' => date('Y-m-d H:i:s'),
			);

			// Load form template
			$this->template->load_partial('admin/master', 'admin/publish/category_form', $data);
		} else {
			if ($this->form_validation->run() == FALSE) {	
				$data = array(
					'query' => false,
					'image_id' => false,
					'image_name' => false,
					'date' => '',
				);

				// load form template
				$this->template->load_partial('admin/master', 'admin/publish/category_form', $data);
			} else {
				$insert_id = $this->adminmodel->save_category();			

				// Clear cache files if auto clear is enabled
				if($this->config->item('cache_auto_clear'))
					$this->output->clear_all_cache();

				// Generate routes
				$this->write_route_config();

				// Show the info message
				$this->session->set_flashdata('info', $this->lang->line('success_insert'));
				
				// When the category is saved, redirect to specific location based on which button is clicked
				admin_redirect('publish/category', $insert_id);
			}
		}
	}

	/**
	 * Edit category
	 */	
	function edit($id) {	
		$this->auth->restrict(2);

		// Validation rules
		$this->form_validation->set_rules($this->rules);
		$this->load->helper('wysiwyg');

		// Get category data
		$query = $this->adminmodel->get_category($id)->row();

		if (!$this->input->post('save')) {		
			$data = array(
				'query' => $query,
				'image_id' => $query->image_id,
				'image_name' => $query->image_name,
				'image_alt' => $query->image_alt,
				'image_title' => $query->image_title,
			);

			// Load the template
			$this->template->load_partial('admin/master', 'admin/publish/category_form', $data);
		} else {
			if ($this->form_validation->run() == FALSE) {	
				$data = array(
					'query' => false,
					'image_id' => $query->image_id,
					'image_name' => $query->image_name,
					'date' => '',
				);

				// Load the template
				$this->template->load_partial('admin/master', 'admin/publish/category_form', $data);
			} else {
				$insert_id = $this->adminmodel->save_category();

				// Clear cache files if auto clear is enabled
				if($this->config->item('cache_auto_clear')) {
					$this->output->clear_all_cache();
				}

				// Generate routes
				$this->write_route_config();

				// Show the info message
				$this->session->set_flashdata('info', $this->lang->line('success_update'));
				
				// When the category is saved, redirect to specific location based on which button is clicked
				admin_redirect('publish/category', $insert_id);
			}
		}
	}

	/**
	 * Delete the Category
	 */	
	function delete($id) {
		$this->auth->restrict(2);
		$this->adminmodel->delete_category($id);

		// Clear cache files if auto clear is enabled
		if($this->config->item('cache_auto_clear')) {
			$this->output->clear_all_cache();
		}

		// Generate routes
		$this->write_route_config();

		// Show info message
		$this->session->set_flashdata('info', $this->lang->line('success_delete'));

		// Redirect to categories
		admin_redirect('publish/category', $id);
	}

	/**
	 * Write the routes into config
	 */	
	function write_route_config() {
		// get all active categories
		$categories = $this->db->where('level', 1);
		$categories = $this->commonmodel->get(array('table' => CATEGORIES_DB_TABLE, 'status' => 1));
		$total_categories = $categories['num_rows'];
		$categories = $categories['query']->result();

		$tpl = APPPATH . 'modules/publish/config/routes-tpl.php';
		$output = APPPATH . 'modules/publish/config/routes.php';
		$routes = '';

		// Iterate through each category
		if($total_categories > 0) {
			$i = 1;
			foreach ($categories as $category) {
				$lang = ($category->lang == $this->config->item('default_language')) ? '' : $category->lang . '/';
				$routes .= $lang . $category->slug;
				if(count($categories) > 1) {
					if ($i < count($categories)) {
						$routes .= '|';
					}
					$i++;
				}
			}
			$data = str_replace("%URLS%",$routes,file_get_contents($tpl));
		} else {
			$data = str_replace("%URLS%","%URLS%",file_get_contents($tpl));
		}

		// Chmod the file
		@chmod($output,0777);

		// Write new stuff to the file
		$handle = fopen($output,'w+');

		// Verify file permissions
		if(is_writable($output)) {

			// Write the file
			if(fwrite($handle,$data)) {
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}

	/**
	 * Check if category with the same url already exist
	 */	
	function _check_slug($str) {	
		$slug = trim($str);
		$id = $this->uri->segment(5);
		
		$total_rows = $this->commonmodel->get(array('table' => CATEGORIES_DB_TABLE, 'slug' => $slug, 'lang' => $this->input->post('lang')));
		$total_rows = $total_rows['num_rows'];

		if ($id == FALSE) {
			// when creating a new item, check if slug already exists
			if ($total_rows == 0) {
				return true;
			} else {
				$this->form_validation->set_message('_check_slug', $this->lang->line('slug_error'));
				return false;
			} 
		} else {
			// on update, check if new slug is entered and if already exists
			if ($total_rows == 0) {
				return true;
			} else {
				// check if slug is the same as the current item slug		
				$q = $this->commonmodel->get(array('table' => CATEGORIES_DB_TABLE, 'id' => $id));
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