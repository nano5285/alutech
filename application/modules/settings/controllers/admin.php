<?php

class Admin extends CI_Controller
{
	function __construct() {
		parent::__construct();
		$this->lang->load('english', 'english');
		$this->template->set('controller', $this);
		$this->auth->restrict(4);

		$this->output->enable_profiler($this->config->item('enable_profiler'));
	}

	function translate() {
		$this->load->library('form_validation');

		// Default labels
		$data['labels'] = array(
			'add_article' => 'Add article',
			'add_post' => 'Add/edit post',
			'incorrect_login' => 'Incorrect username or password!',
			'add_user' => 'Add/edit user',
			'admin_translations' => 'Administration translations',
			'add_new_page' => 'Add/edit page',
			'all_languages' => 'All languages',
			'author' => 'Author',
			'back' => 'Back',
			'bio' => 'Bio',
			'blank' => 'Blank window',
			'blocks' => 'Blocks',
			'cache_deleted' => 'Cache files successfully deleted',
			'category' => 'Category',
			'categories' => 'Categories',
			'change_order' => 'Reorder Menu Items',
			'change_order_help' => 'Use drag & drop method to rearrange the items',
			'clear_cache' => 'Clear cache files',
			'common_category' => 'Common category',
			'confirmation_title' => 'Confirmation required',
			'content' => 'Content',
			'cancel' => 'Cancel',
			'confirm' => 'Confirm',
			'confirm_password' => 'Confirm password',
			'created' => 'Created',
			'custom_fields' => 'Custom fields',
			'dashboard' => 'Dashboard',
			'delete' => 'Delete',
			'delete_selected' => 'Delete selected',
			'delete_confirmation' => 'Are you sure you want to delete this item?',
			'delete_confirmation_user' => 'This user has created a number of posts. Transfer posts to another user',
			'description' => 'Description',
			'edit' => 'Edit',
			'edit_profile' => 'Edit profile',
			'edit_menu_items' => 'Edit Menu Items',
			'enter_category_slug' => 'Enter the category url',
			'enter_category_title' => 'Enter the category title',
			'enter_menu_url' => 'You must enter the item url',
			'enter_menu_title' => 'You must enter the item title',
			'enter_search_term' => 'Enter search term',
			'enter_block_content' => 'You must enter the block content',
			'enter_block_identifier' => 'You must enter the block identifier',
			'excerpt' => 'Excerpt',
			'featured' => 'Featured',
			'filter' => 'Filter',
			'identifier' => 'Identifier',
			'identifier_exists' => 'The same identifier already exists',
			'image' => 'Image',
			'images' => 'Images',
			'insufficient_rights' => 'You do not have sufficient rights to access this page!',
			'js_disabled' => 'This site requires JavaScript. Please enable JavaScript in your browser.',
			'level' => 'Level',
			'logged_in_as' => 'You are logged in as',
			'leave_password' => 'Leave password fields blank if you do not want to change the password.',
			'login' => 'Login',
			'language' => 'Language',
			'login_to_admin' => 'Login to administration panel',
			'logout' => 'Logout',
			'login_to_admin' => 'Login to ACMS admin panel',
			'max_length' => 'The %s field can not exceed %s characters in length',
			'menus' => 'Menus',
			'meta_keywords' => 'Meta keywords',
			'meta_description' => 'Meta description',
			'min_length' => 'The %s field must be at least %s characters in length',
			'matches' => 'The %s field does not match the %s field',
			'min_length' => 'The %s field must be at least %s characters in length',
			'name' => 'Name',
			'new_item_title' => 'New item title',
			'new_item_url' => 'New item url',
			'new_block' => 'Add/edit block',
			'new_category' => 'Add/edit category',
			'new_menu' => 'Add/edit menu',
			'pages' => 'Pages',
			'page_blocks' => 'Page blocks',
			'parent_category' => 'Parent category',
			'password' => 'Password',
			'position' => 'Position',
			'posts' => 'Posts',
			'publish' => 'Publish',
			'publish_date' => 'Publish date',
			'published' => 'Published',
			'required' => 'The %s field is required',
			'seo_title' => 'Optimized H1 title',
			'settings' => 'Settings',
			'save_list' => 'Save',
			'save_edit' => 'Save and continue editing',
			'save_new' => 'Save and add new',
			'select_category' => 'Select Category',
			'success_insert' => 'Successfuly created',
			'success_update' => 'Successfuly updated',
			'success_delete' =>'Successfuly deleted',
			'slug_error' => 'URL already exists',
			'target' => 'Target',
			'translate' => 'Translations',
			'template' => 'Template',
			'title' => 'Title',
			'toggle_editor' => 'Toggle visual editor',
			'translation' => 'Translation',
			'upload_invalid_filetype' => 'The filetype you are attempting to upload is not allowed',
			'upload_invalid_filesize' => 'The file you are attempting to upload is larger than the permitted size',
			'username_exist' => 'Choosen username already exists',
			'username' => 'Username',
			'url' => 'URL',
			'users' => 'Users',
			'user_delete_error' => 'You can not delete the last available user or yourself',
			'user_group' => 'User group',
			'valid_email' => 'The %s field must contain a valid email address',
		);

		if ( $this->input->post('save') ) {
			$this->load->helper('file');

			// Save labels to the lang file
			$lang_path = APPPATH . 'language/english/english_lang.php';
			$upload_lang_path = APPPATH . 'language/english/upload_lang.php';

			write_file($lang_path, "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');" . "\n\n", 'w+');
			write_file($upload_lang_path, "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');" . "\n\n", 'w+');

			foreach ($data['labels'] as $key => $value) {
				if ( $this->input->post($key) == "" ) {
					$label = $value;
				} else {
					$label = $this->input->post($key);
				}
				if($key == 'upload_invalid_filetype' OR $key == 'upload_invalid_filesize') {
					write_file($upload_lang_path, '$lang["'.$key.'"] = "'.$label.'";' . "\n", 'a+');
				}
				write_file($lang_path, '$lang["'.$key.'"] = "'.$label.'";' . "\n", 'a+');
			}
			
			// Wait three seconds to make sure that file is written before refresh
			sleep(3);

			$this->session->set_flashdata('info', $this->lang->line('success_update'));
			redirect($this->uri->uri_string());
		} else {
			$this->template->load_partial('admin/master', 'admin/settings/translate', $data);
		}
	}

	function clear_cache() {
		$this->output->clear_all_cache();			
		$this->session->set_flashdata('info', $this->lang->line('cache_deleted'));
		redirect($this->config->item('base_url') . ADMIN_URL . '/settings');
	}
}