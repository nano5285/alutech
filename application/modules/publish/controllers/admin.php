<?php

class Admin extends CI_Controller
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
	
	private $rules = array(
						array(
							'field'   => 'lang',
							'label'   => 'lang:language',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'category',
							'label'   => 'lang:category',
							'rules'   => 'required',
						),
						array(
							'field'   => 'author',
							'label'   => 'lang:author',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'title',
							'label'   => 'lang:title',
							'rules'   => 'trim|required',
						),
						array(
							'field'   => 'slug',
							'label'   => 'lang:url',
							'rules'   => 'trim|required|callback__check_slug',
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
							'field'   => 'featured',
							'label'   => 'lang:featured',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'excerpt',
							'label'   => 'lang:excerpt',
							'rules'   => 'trim',
						),
						array(
							'field'   => 'content',
							'label'   => 'lang:content',
							'rules'   => 'trim',
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
					);		

	/**
	 * List Posts
	 */
	function index($num = '') {
		$this->auth->restrict(2);
		$this->load->library('pagination');

		// Get filter query strings
		$search_query = $this->input->get('q');
		$search_lang = $this->input->get('lang');

		// Running search or regular query
		if ( !empty($search_query) || !empty($search_lang) ) {
			$query = $this->adminmodel->get_posts(array('search_query' => $search_query, 'lang' => $search_lang));
		} else {
			$config['per_page'] = $this->config->item('admin_items_per_page');

			// Get all available posts
			$query = $this->adminmodel->get_posts(array('limit' => $config['per_page'], 'offset' => $num));

			// Configure and initialize pagination
			$config['base_url'] = $this->config->item('base_url') . ADMIN_URL . '/publish/posts';
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

		$this->template->load_partial('admin/master', 'admin/publish/index', $data);
	}

	/**
	 * Create Post
	 */
	function create() {			
		$this->auth->restrict(2);
		$this->load->helper('wysiwyg');

		$this->form_validation->set_rules($this->rules);

		// Validation rules for custom page fields
		$custom_fields = $this->config->item('publish_custom_fields');
		if ($custom_fields) {
			foreach ($custom_fields as $field => $value) {
				$this->form_validation->set_rules('custom_field['.$field.'][content]', '', 'trim');
			}
		}

		$data = array(
			'query' => false,
			'authors' => $this->adminmodel->get_authors()->result(),
			'images' => '',
		);

		/**
		 * If save button is not clicked, load the blank form template
		 * 
		 * If save button is clicked and if the form does not validate,
		 * reload the form template and repopulate the values
		 *
		 * If the form does validate, save a new post
		 */
		if (!$this->input->post('save')) {		
			$data['lang'] = $this->config->item('default_language');
			$data['date'] = date('Y-m-d H:i:s'); // If the form is not submitted, set current date and time

			$this->template->load_partial('admin/master', 'admin/publish/form', $data);
		} else {
			if ($this->form_validation->run() == FALSE) {	
				$data['lang'] = $this->input->post('lang');
				$data['date'] = '';

				$this->template->load_partial('admin/master', 'admin/publish/form', $data);
			} else {
				$insert_id = $this->adminmodel->save_post();
				
				// Clear cache files if auto clear is enabled
				if($this->config->item('cache_auto_clear')) {
					$this->output->clear_all_cache();
				}

				$this->session->set_flashdata('info', $this->lang->line('success_insert'));			
				admin_redirect('publish/posts', $insert_id);
			}
		}
	}	

	/**
	 * Edit Post
	 */	
	function edit($id) {
		$this->auth->restrict(2);
		$this->load->helper('wysiwyg');

		$this->form_validation->set_rules($this->rules);

		// Validation rules for custom page fields
		$custom_fields = $this->config->item('publish_custom_fields');
		if ($custom_fields) {
			foreach ($custom_fields as $field => $value) {
				$this->form_validation->set_rules('custom_field['.$field.'][id]', '', 'trim');
				$this->form_validation->set_rules('custom_field['.$field.'][identifier]', '', 'trim');
				$this->form_validation->set_rules('custom_field['.$field.'][content]', '', 'trim');
			}
		}

		$images = $this->commonmodel->get(array('table' => FILES_DB_TABLE, 'module' => PUBLISH_M, 'item_id' => $id, 'sort_column' => 'position', 'sort_direction' => 'asc'));
		$categories = $this->commonmodel->get(array('table' => CATEGORIES_DB_TABLE, 'id' => $id));
		$data = array(
			'images' => $images['query']->result(),
			'authors' => $this->adminmodel->get_authors()->result(),
			'categories' => $categories['query']->result()
		);

		if (!$this->input->post('save')) {
			// If the form is not submitted, get the post from db
			$query = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'id' => $id));
			$data['query'] = $query['query']->row();
			$data['lang'] = $query['query']->row()->lang;
			
			$this->template->load_partial('admin/master', 'admin/publish/form', $data);
		} else {
			/**
			 * If the form does not validate...
			 * clear default query and use currently entered/changed data
			 */
			if ($this->form_validation->run() == FALSE) {
				$data['query'] = false;
				$data['id'] = $id;
				$data['date'] = '';
				$data['lang'] = $this->input->post('lang');
				$this->template->load_partial('admin/master', 'admin/publish/form', $data);
			} else {
				$id = $this->adminmodel->save_post();

				// Clear cache files if auto clear is enabled
				if($this->config->item('cache_auto_clear'))
					$this->output->clear_all_cache();
				
				$this->session->set_flashdata('info', $this->lang->line('success_update'));
				admin_redirect('publish/posts', $id);
			}
		}
	}

	/**
	 * Delete Post
	 */		
	function delete($id) {
		$this->auth->restrict(2);

		// Delete the post from db
		$this->commonmodel->delete(array('table' => POSTS_DB_TABLE, 'id' => $id, 'module' => PUBLISH_M));
		
		// Delete all post custom blocks
		$this->commonmodel->delete(array('table' => POST_META_DB_TABLE, 'post_id' => $id, 'module' => PUBLISH_M));

		// Delete post images from the db and uploads folder...
		$files = $this->adminmodel->get_images($id);
		foreach ($files as $file) {
			$file_path = './' . $this->config->item('upload_folder') . $file->name;
			if(file_exists($file_path)) {
				unlink($file_path);
			}
		}
		$this->adminmodel->delete_images_from_db($id,PUBLISH_M);

		// Clear cache files if auto clear is enabled
		if($this->config->item('cache_auto_clear')) {
			$this->output->clear_all_cache();
		}

		$this->session->set_flashdata('info', $this->lang->line('success_delete'));
		admin_redirect('publish/posts', $id);
	}

	/**
	 * Updating categories based on choosen language (ajax)
	 * Passing the lang code to model which will return/update the option/category values
	 */	 
	function get_admin_category_tree() {
		$lang = $this->input->post('lang');
		$this->adminmodel->get_admin_category_tree($lang);
	}

	/**
	 * Check if post with the same url already exist
	 */	
	function _check_slug($str) {	
		$slug = trim($str);
		$id = $this->uri->segment(5);
		
		$total_rows = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'module' => PUBLISH_M, 'slug' => $slug, 'lang' => $this->input->post('lang')));
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
				$q = $this->commonmodel->get(array('table' => POSTS_DB_TABLE, 'id' => $id));
				if ($slug == $q['query']->row()->slug) {
					return true;
				} else {
					$this->form_validation->set_message('_check_slug', $this->lang->line('slug_error'));
					return false;
				}
			}		
		}
	}
	
}