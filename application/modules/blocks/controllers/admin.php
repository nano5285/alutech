<?php

class Admin extends CI_Controller
{
	function __construct() {
		parent::__construct();

		// Redirect if module is disabled
		if(!in_array('blocks', $this->config->item('cms_modules'))) {
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
								'field'   => 'title',
								'label'   => 'lang:title',
								'rules'   => 'trim',
							),
							array(
								'field'   => 'content',
								'label'   => 'lang:content',
								'rules'   => 'trim',
							),
							array(
								'field'   => 'use_editor',
								'label'   => 'lang:editor',
								'rules'   => 'trim',
							),
						);	

	/**
	 * List blocks
	 */			
	function index($num = '') {
		$this->auth->restrict(3);
		$this->load->library('pagination');

		// Filter query strings
		$search_query = $this->input->get('q');
		$search_lang = $this->input->get('lang');

		/**
		 * If search is activated (keyword or search language is defined) get all blocks that match the search criteria
		 * If search is not activated, get default block list
		 */	
		if ( !empty($search_query) OR !empty($search_lang) ) {
			$query = $this->commonmodel->get(array('table' => POST_META_DB_TABLE, 'identifier' => true, 'module' => BLOCKS_M, 'search_query' => $search_query, 'lang' => $search_lang));
		} else {		
			$config['per_page'] = $this->config->item('admin_items_per_page');
			$query = $this->commonmodel->get(array('table' => POST_META_DB_TABLE, 'module' => BLOCKS_M, 'limit' => $config['per_page'], 'offset' => $num));

			// Configure and initialize pagination
			$config['base_url'] = $this->config->item('base_url') . ADMIN_URL . '/blocks';
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

		$this->template->load_partial('admin/master', 'admin/blocks/index', $data);
	}

	/**
	 * Create a new block
	 *
	 * First, validation rules are set for the block fields
	 *
	 * If the form does not validate or if it is blank (opened for the first time)
	 * load the admin template with empty form or repopulated values
	 *
	 * If passed validation, save the block and redirect to proper URL
	 */			
	function create() {	
		$this->auth->restrict(4);
		$this->form_validation->set_rules($this->rules);

		if ($this->form_validation->run() == FALSE) {
			$this->load->helper('wysiwyg');
			$data['query'] = false;
			$this->template->load_partial('admin/master', 'admin/blocks/form', $data);
		} else {
			$insert_id = $this->adminmodel->save();
			$this->session->set_flashdata('info', $this->lang->line('success_insert'));
			admin_redirect('blocks', $insert_id);
		}
	}

	/**
	 * Edit block
	 *
	 * First, validation rules are set for the page fields
	 *
	 * If the block is not saved (save button is not pressed),
	 * get the block data form database and populate the template
	 *
	 * If the block is saved (save button is pressed), run the validation.
	 * If the form does not validate, reload the admin template with repopulated values
	 *
	 * If passed validation, save the block and redirect to proper URL
	 */		
	function edit($id) {			
		$this->auth->restrict(3);
		$this->form_validation->set_rules($this->rules);
		$this->load->helper('wysiwyg');

		if (!$this->input->post('save')) {
			$q = $this->commonmodel->get(array('table' => POST_META_DB_TABLE, 'id' => $id));
			$data['query'] = $q['query']->row();
			$this->template->load_partial('admin/master', 'admin/blocks/form', $data);
		} else {
			if ($this->form_validation->run() == FALSE) {
				// Clear the query and use repopulated form values
				$data['query'] = false;
				$this->template->load_partial('admin/master', 'admin/blocks/form', $data);
			} else {
				$id = $this->adminmodel->save();				
				$this->session->set_flashdata('info', $this->lang->line('success_update'));
			
				// Clear cache files if auto clear is enabled
				if($this->config->item('cache_auto_clear')) {
					$this->output->clear_all_cache();
				}

				admin_redirect('blocks', $id);
			}
		}			
	}

	/**
	 * Delete block
	 *
	 * Delete the block from the database and redirect to the index (block list) template
	 *
	 */	
	function delete($id) {
		$this->auth->restrict(4);
		$this->commonmodel->delete(array('table' => POST_META_DB_TABLE, 'id' => $id, 'module' => BLOCKS_M));	
		$this->session->set_flashdata('info', $this->lang->line('success_delete'));
		admin_redirect('blocks', $id);
	}

}