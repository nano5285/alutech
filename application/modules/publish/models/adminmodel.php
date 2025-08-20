<?php
class Adminmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
	 * Get Categories
	 */
	function categories($options = array()) {
		$this->db->select(
			CATEGORIES_DB_TABLE.'.*,
			'.FILES_DB_TABLE.'.id AS image_id,
			'.FILES_DB_TABLE.'.name AS image_name'
		);
		$this->db->join(FILES_DB_TABLE, 'item_id = '.CATEGORIES_DB_TABLE.'.id AND module='.CATEGORIES_M, 'left');

		// If searching, add like clause
		if(isset($options['search_query']) AND isset($options['lang'])) {
			if ($options['lang']) {
				$this->db->where('lang', $options['lang']);
			}
			$term = strtolower($options['search_query']);
			$this->db->like(array(CATEGORIES_DB_TABLE.'.title' => $term, CATEGORIES_DB_TABLE.'.slug' => $term));
		}

		$tempdb = clone $this->db;

		// If limit / offset is declared
		if(isset($options['offset'])) {
			$this->db->limit($options['limit'], $options['offset']);
		} else if(isset($options['limit'])) {
			$this->db->limit($options['limit']);
		}

		$this->db->order_by(CATEGORIES_DB_TABLE.'.position', 'asc');
		
		return array(
			'num_rows' => $tempdb->get(CATEGORIES_DB_TABLE)->num_rows(),
			'query' => $this->db->get(CATEGORIES_DB_TABLE),
		);
	}


	/**
	 * Get category
	 */		
	function get_category($id) {
		$this->db->select(
			CATEGORIES_DB_TABLE.'.*,
			'.FILES_DB_TABLE.'.id AS image_id,
			'.FILES_DB_TABLE.'.name AS image_name,
			'.FILES_DB_TABLE.'.title AS image_title,
			'.FILES_DB_TABLE.'.alt AS image_alt'
		);

		$this->db->join(FILES_DB_TABLE, 'item_id = '.CATEGORIES_DB_TABLE.'.id AND module='.CATEGORIES_M, 'left');
		$this->db->where(CATEGORIES_DB_TABLE.'.id', $id);
 		return $this->db->get(CATEGORIES_DB_TABLE);
	}

	/**
	 * Save category
	 */		
	function save_category() {	
		$id = $this->uri->segment(5);
		$langs = $this->config->item('lang_desc');

		// Category position
		$position = preg_replace("/[^0-9\.]/", '', $this->input->post('position'));
		$item_position = explode('.', $position);
		$level = count($item_position);

		// Get item parent id
		unset($item_position[count($item_position) - 1]);
		if($level > 1) {
			$parent_position = implode('.', $item_position);
			$parent_id = $this->db->get_where(CATEGORIES_DB_TABLE, array('position' => $parent_position, 'lang' => $this->input->post('lang')), 1)->row()->id;
			$parent_id = $parent_id;
		} else {
			$parent_id = 0;
		}

		// Category input data
		$category_data = array(
			'position' => $position,
			'parent' => $parent_id,
			'level' => $level,
			'title' => $this->input->post('title'),
			'slug' => $this->input->post('slug'),	
			'status' => $this->input->post('status'),
			'content' => $this->input->post('content'),
			'lang' => $this->input->post('lang'),
			'tpl' => $this->input->post('template'),
			'seo_title' => $this->input->post('seo_title'),
			'meta_keywords' => $this->input->post('meta_keywords'),
			'meta_description' => $this->input->post('meta_description'),
			'date' => $this->input->post('date'),
			'modified' => date('Y-m-d H:i:s'),
		);

		/**
		 * If category ID does not exist (not provided in the URL), insert new category.
		 * Else if category ID does exist, update the current category. 
		 */
		if (!$id) {
			$category_data['author'] = $this->session->userdata('user_id');
			$this->db->insert(CATEGORIES_DB_TABLE, $category_data);
		
			// New category id
			$item_id = $this->db->insert_id();

			// Upload and return image name
			$image = $this->upload_image();

			// If image is uploaded, save it to the db
			if($image) {
				$image_data = array(
					'module' => CATEGORIES_M,
					'item_id' => $item_id,
					'name' => $image,
					'title' => $this->input->post('image_title'),
					'alt' => $this->input->post('image_alt'),
					'status' => 1,
					'date' => date('Y-m-d H:i:s'),
					'modified' => date('Y-m-d H:i:s'),
				);
				$this->db->insert(FILES_DB_TABLE, $image_data);
			}

			return $item_id;
		} else {
			$this->db->where('id', $id)->limit(1)->update(CATEGORIES_DB_TABLE, $category_data);

			// Current image id and name
			$image_id = $this->input->post('image_id');
			$current_image_name = $this->input->post('image_name');

			/**
			 * If delete image checkbox is selected,
			 * delete the image from db and uploads folder
			 */
			if ($this->input->post('delete_image')) {
				$this->delete_image_from_db($image_id);
				$this->unlink_file($current_image_name);
			} else {
				$image_data = array(
					'title' => $this->input->post('image_title'),
					'alt' => $this->input->post('image_alt'),
					'modified' => date('Y-m-d H:i:s'),
				);

				// Upload the new image if it is selected
				$image = $this->upload_image();
				if ($image) {
					$image_data['name'] = $image;
					$image_data['date'] = date('Y-m-d H:i:s');

					// Delete old image from the folder
					if ($current_image_name) {
						$this->unlink_file($current_image_name);
					}
				}

				// Insert a new image or update the existing one if the image already exist
				$total_images = $this->db->get_where(FILES_DB_TABLE, array('item_id' => $id, 'module' => CATEGORIES_M))->num_rows();
				if ($total_images > 0) {
					$this->db->where(array('item_id' => $id, 'module' => CATEGORIES_M))->limit(1)->update(FILES_DB_TABLE, $image_data);
				} else {
					$image_data['module'] = CATEGORIES_M;
					$image_data['item_id'] = $id;
					$image_data['status'] = 1;
					$image_data['date'] = date('Y-m-d H:i:s');
					$image_data['modified'] = date('Y-m-d H:i:s');

					$this->db->insert(FILES_DB_TABLE, $image_data);
				}
			}

			return $id;
		}
	}

	/**
	 * Get categories
	 */
	function get_categories($id) {
		return $this->db->get_where(CATEGORIES_DB_TABLE, array('id' => $id))->result();
	}

	/**
	 * Category tree
	 */
	function get_admin_category_tree($lang, $parent = 0, $cat_id = '') {
		$lang = ($lang == FALSE) ? $this->config->item('language_abbr') : $lang;

		$items = $this->db->order_by('position', 'asc')
						  ->where('lang', $lang)
						  ->get(CATEGORIES_DB_TABLE)
						  ->result();

		if(sizeof($items)) {
			foreach($items as $row) {
				$selected = ($row->id == $cat_id) ? ' selected="selected"' : '';
				$level = ($row->level != 1) ? str_repeat('--', $row->level) : '';
				echo '<option value="'.$row->id.'"'.set_select('category',$row->id).$selected.'>'.$level.$row->title.'</option>';
			}
		} else {
			echo '<option value="">-</option>';
		}	
	}

	/**
	 * Delete the category and related images
	 */
	function delete_category($id) {
		$this->db->where('id', $id)->delete(CATEGORIES_DB_TABLE);
		$files = $this->get_images($id,CATEGORIES_M);
		$this->unlink_file($files[0]->name);
		$this->delete_images_from_db($id,CATEGORIES_M);
	}

	/**
	 * Get Posts
	 */
	function get_posts($options = array()) {
		$dbprefix = $this->db->dbprefix;
		$this->db->select(
				POSTS_DB_TABLE.'.*, 
				'.CATEGORIES_DB_TABLE.'.title AS category_title,
				'.CATEGORIES_DB_TABLE.'.slug AS category_slug,
				'.CATEGORIES_DB_TABLE.'.parent AS category_parent,
				'.CATEGORIES_DB_TABLE.'.position AS category_position,
				'.USERS_DB_TABLE.'.name AS author,
				GROUP_CONCAT(DISTINCT '.$dbprefix.FILES_DB_TABLE.'.name ORDER BY '.$dbprefix.FILES_DB_TABLE.'.position ASC) AS images', false
		);

		$this->db->join(USERS_DB_TABLE, USERS_DB_TABLE.'.id = '.POSTS_DB_TABLE.'.author', 'left');
		$this->db->join(CATEGORIES_DB_TABLE, CATEGORIES_DB_TABLE.'.id = '.POSTS_DB_TABLE.'.category', 'left');
		$this->db->join(FILES_DB_TABLE, FILES_DB_TABLE.'.item_id = '.POSTS_DB_TABLE.'.id AND '.$dbprefix.FILES_DB_TABLE.'.module='.PUBLISH_M, 'left');

		// If searching, add like clause
		if(isset($options['search_query']) AND isset($options['lang'])) {
			if ($options['lang']) {
				$this->db->where(POSTS_DB_TABLE.'.lang', $options['lang']);
			}
			$this->db->like(POSTS_DB_TABLE.'.title', $options['search_query']);
		}

		// Filter posts and return results
		$this->db->where(POSTS_DB_TABLE.'.module', PUBLISH_M)
				 ->group_by(POSTS_DB_TABLE.'.id')
				 ->order_by(POSTS_DB_TABLE.'.date', 'desc');

		$tempdb = clone $this->db;

		// If limit / offset is declared
		if(isset($options['offset'])) {
			$this->db->limit($options['limit'], $options['offset']);
		} else if(isset($options['limit'])) {
			$this->db->limit($options['limit']);
		}
			
		return array(
			'num_rows' => $tempdb->get(POSTS_DB_TABLE)->num_rows(),
			'query' => $this->db->get(POSTS_DB_TABLE),
		);
	}

	/**
	 * Save The Post
	 */
	function save_post() {
		$id = $this->uri->segment(5);

		// Post data
		$post_data = array(
			'module' => PUBLISH_M,
			'lang' => $this->input->post('lang'),
			'author' => $this->input->post('author'),
			'title' => $this->input->post('title'),
			'slug' => $this->input->post('slug'),
			'status' => $this->input->post('status'),
			'category' => $this->input->post('category'),
			'featured' => $this->input->post('featured'),
			'excerpt' => $this->input->post('excerpt'),
			'content' => $this->input->post('content'),		
			'seo_title' => $this->input->post('seo_title'),
			'meta_keywords' => $this->input->post('meta_keywords'),
			'meta_description' => $this->input->post('meta_description'),
			'date' => $this->input->post('date'),
			'modified' => date('Y-m-d H:i:s'),
		);

		/**
		 * If post ID does not exist (not provided in the URL), insert a new post.
		 * Else if post ID does exist, update the current post. 
		 */
		if (!$id) {
			$this->db->insert(POSTS_DB_TABLE, $post_data);

			// New post id
			$id = $this->db->insert_id();

			// Process new post images
			if($this->config->item('publish_images') == TRUE) {
				for($i = 1; $i <= $this->config->item('publish_image_fields'); $i++) {
					$image = $this->upload_image('userfile'.$i);

					if ($image) {
						$image_data = array(
							'date' => date('Y-m-d H:i:s'),
							'modified' => date('Y-m-d H:i:s'),
							'name' => $image,
							'module' => PUBLISH_M,
							'item_id' => $id,
							'status' => 1,
							'title' => $this->input->post('image_title'.$i),
							'alt' => $this->input->post('image_alt'.$i),
							'position' => $i
						);

						$this->db->insert(FILES_DB_TABLE, $image_data);
					}
				}
			}
		} else {
			// Update the current post
			$this->db->where('id', $id)->update(POSTS_DB_TABLE, $post_data);

			// Process new post images
			if($this->config->item('publish_images') == TRUE) {
				$image_position = $this->db->select_max('position')->where(array('item_id' => $id, 'module' => PUBLISH_M))->get(FILES_DB_TABLE)->row()->position;

				for($i = 1; $i <= $this->config->item('publish_image_fields'); $i++) {
					$image = $this->upload_image('userfile'.$i);

					if ($image) {
						$image_data = array(
							'date' => date('Y-m-d H:i:s'),
							'modified' => date('Y-m-d H:i:s'),
							'name' => $image,
							'module' => PUBLISH_M,
							'item_id' => $id,
							'status' => 1,
							'title' => $this->input->post('image_title'.$i),
							'alt' => $this->input->post('image_alt'.$i),
							'position' => $image_position + $i
						);

						$this->db->insert(FILES_DB_TABLE, $image_data);
					}
				}

				// Update existing images
				$images = $this->input->post('image');
				if($images) {
					foreach($images as $image) {
						$image_id = $image['image_id'];
						$delete_image = (isset($image['delete'])) ? $image['delete'] : '';
						$image_file = $image['image_file'];
						$new_image_file = 'new_image_file'.$image_id;
						
						/**
						 * If delete image checkbox is selected, delete the image from db and uploads folder
						 */
						if($delete_image) {
							$this->delete_image_from_db($image_id);
							$this->unlink_file($image['image_file']);
						} else {
							$image_data = array(
								'position' => $image['image_position'],
								'title' => $image['image_title'],
								'alt' => $image['image_alt'],
								'modified' => date('Y-m-d H:i:s')
							);

							// Upload new image if selected
							$image = $this->upload_image($new_image_file);
							if ($image) {
								$image_data['name'] = $image;
								$this->unlink_file($image_file);
							}

							// Update image db
							$this->db->where('id', $image_id)->limit(1)->update(FILES_DB_TABLE, $image_data);
						}
					}
				}
			}
		}

		// Processing custom fields
		$this->commonmodel->save_post_meta(array('post_id' => $id, 'module' => PUBLISH_M));

		return $id;
	}

	/**
	 * Get images
	 */
	function get_images($id, $module = PUBLISH_M) {
		return $this->db->get_where(FILES_DB_TABLE, array('item_id' => $id, 'module' => $module))->result();
	}

	/**
	 * Get authors
	 */
	function get_authors() {
		return $this->db->get(USERS_DB_TABLE);
	}

	/**
	 * Upload image
	 */
	function upload_image($field = 'userfile') {
		// If an image is selected		
		if ( basename($_FILES[$field]['name']) != "" ) {
			$image_folder = $this->config->item('upload_folder');

			$config['upload_path'] = $image_folder;
			$config['allowed_types'] = $this->config->item('upload_allowed_types');
			$config['max_size']	= $this->config->item('upload_max_size');
			$this->load->library('upload', $config);
			
			// Check if image upload folder already exist. if not, create it
			if(!is_dir($image_folder)) {
				mkdir($image_folder,$this->config->item('folder_permission'));
			}
			
			// Upload new image
			if ($this->upload->do_upload($field)){
				$upload_data = $this->upload->data();

				// Get the image name
				$image = $upload_data['file_name'];
			} else {
				$image = false;
				$error = $this->upload->display_errors();
				$this->session->set_flashdata('error', $error);
			}
			return $this->security->sanitize_filename($image);
		} else {
			return false;
		}
	}

	/**
	 * Delete the image from DB
	 */
	function delete_image_from_db($id) {
		$this->db->where(array('id' => $id))->limit(1)->delete(FILES_DB_TABLE);
	}

	/**
	 * Delete images from DB
	 */
	function delete_images_from_db($item_id, $module) {
		$this->db->where(array('item_id' => $item_id, 'module' => $module))->delete(FILES_DB_TABLE);
	}

	/**
	 * Delete the image file
	 */
	function unlink_file($file_name = FALSE) {
		$upload_path = $this->config->item('upload_folder');

		if($file_name) {
			if(file_exists($upload_path . $file_name)) {
				unlink($upload_path . $file_name);
			}
		}
	}
	
}
?>