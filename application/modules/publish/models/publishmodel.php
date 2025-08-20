<?php
class Publishmodel extends CI_Model
{
	function __construct() {
		parent::__construct();
	}	

	/**
	 * Get posts
	 */	
	function get_posts($options = array()) {
		// Default values
		$options = array_merge(array(
			'category' => '',
			'per_page' => 5, 
			'offset' => 0,
			'orderby' => $this->config->item('publish_orderby'),
			'order_direction' => $this->config->item('publish_order_direction'),
			'featured' => '',
			'post_id' => '',
			'published_only' => 1,
			'children' => TRUE,
		), $options);

		$lang = $this->config->item('language_abbr');
		$lang_url = ($lang == $this->config->item('default_language')) ? '' : $lang . "/";

		$categories = $this->db->select('id, title, slug, position, level, status')
							   ->order_by(CATEGORIES_DB_TABLE.'.position', 'asc')
							   ->where(array(CATEGORIES_DB_TABLE.'.lang' => $lang, CATEGORIES_DB_TABLE.'.status' => 1))
							   ->get(CATEGORIES_DB_TABLE)
							   ->result();

		$posts = $this->db->select(
							POSTS_DB_TABLE.'.*,
							'.USERS_DB_TABLE.'.name AS author_name,
							'.USERS_DB_TABLE.'.username AS author_username'
						  )
				 ->order_by(POSTS_DB_TABLE.'.'.$options['orderby'], $options['order_direction'])
		         ->join(CATEGORIES_DB_TABLE, CATEGORIES_DB_TABLE.'.id = '.POSTS_DB_TABLE.'.category', 'left')
		         ->join(USERS_DB_TABLE, USERS_DB_TABLE.'.id = '.POSTS_DB_TABLE.'.author', 'left')
		         ->from(POSTS_DB_TABLE)
		         ->group_by(POSTS_DB_TABLE.'.id');

		// get posts by provided category. String (category slug/url) or ID can be provided
		if(isset($options['category']) AND $options['category'] AND $categories) {
			foreach($categories as $category) {
				if($category->slug == $options['category'] OR $category->id == $options['category']) {
					$current_category_id = $category->id;
					$current_category_position = $category->position;
					break;
				}
			}

			if(isset($current_category_id)) {
				if(isset($options['children']) AND $options['children']) {
					$cats = array();
					$parent = strlen($current_category_position);
					foreach($categories as $category) {
						$cat_position = substr($category->position, 0, $parent);

						if($cat_position === $current_category_position) {
							$cats[] = $category->id;
						}
					}
					$posts = $this->db->where_in(POSTS_DB_TABLE.'.category', $cats);
				} else {
					$posts = $this->db->where(POSTS_DB_TABLE.'.category', $current_category_id);
				}
			}
		}

		if(isset($options['post_id']) AND $options['post_id']) {
			$posts = $this->db->where(POSTS_DB_TABLE.'.id', $options['post_id']);
		}
		         
		$this->db->where(array(POSTS_DB_TABLE.'.module' => PUBLISH_M, POSTS_DB_TABLE.'.lang' => $lang));

		if(isset($options['published_only']) AND $options['published_only']) {
			$this->db->where(POSTS_DB_TABLE.'.status', $options['published_only']);
		}
		
		if(isset($options['featured']) AND $options['featured']) {
			$this->db->where(POSTS_DB_TABLE.'.featured', 1);
		}

		$tmp_query = clone $posts;

		$posts = $this->db->limit($options['per_page'], $options['offset'])
						  ->get()
						  ->result();

		// Attach corresponding images and categories to the post
		if($posts) {
			$images = $this->db->select('item_id, name, title, alt, position')
							   ->order_by('position', 'asc')
							   ->where(array(FILES_DB_TABLE.'.module' => PUBLISH_M, FILES_DB_TABLE.'.status' => 1))
							   ->get(FILES_DB_TABLE)
							   ->result();

			foreach ($posts as $index => $post) {				
				$img_array = array();			
				foreach($images as $image) {
					if ($image->item_id == $post->id) {
						$img_array[] = $image;
						$posts[$index]->images = $img_array;
						$posts[$index]->main_image = $img_array[0];
					}
				}

				foreach($categories as $category) {				
					if($category->id == $post->category) {
						$posts[$index]->category = $category;

						$root_cat_permalink = '';
						$cat_position = explode('.', $category->position);
						if(sizeof($cat_position) > 1) {
							$root_cat = $cat_position[0];
							foreach($categories as $rcat) {
								if($rcat->position == $root_cat) {
									$root_cat_permalink = $rcat->slug . '/';
									break;
								}
							}
						}

						$category->permalink = $this->config->item('base_url') . $lang_url . $root_cat_permalink . $category->slug;
					}
				}

				$posts[$index]->permalink = $post->category->permalink . '/' . $post->slug;
			}

			return array(
				'posts' => $posts,
				'total_rows' => $tmp_query->get()->num_rows(),
			);
		}
	}
	
	/**
	 * Get categories
	 */	
	function get_category_data($arr = array()) {
		$dbprefix = $this->db->dbprefix;
		$q = $this->db->select(
						CATEGORIES_DB_TABLE.'.*,
						'.FILES_DB_TABLE.'.name AS image,
						'.FILES_DB_TABLE.'.title AS image_title,
						'.FILES_DB_TABLE.'.alt AS image_alt'
					  )
			 ->order_by(CATEGORIES_DB_TABLE.'.level', 'asc')
	         ->join(FILES_DB_TABLE, FILES_DB_TABLE.'.item_id = '.CATEGORIES_DB_TABLE.'.id AND '.$dbprefix.FILES_DB_TABLE.'.module='.CATEGORIES_M, 'left')
	         ->group_by(CATEGORIES_DB_TABLE.'.id')
	         ->where_in('slug', $arr)
	         ->where('lang', $this->config->item('language_abbr'))
	         ->get(CATEGORIES_DB_TABLE)
	         ->result();

		return ($q) ? $q : false;
	}

	/**
	 * Get post
	 */	
	function get_post($id = '') {
		$lang = $this->config->item('language_abbr');
		$lang_url = ($lang == $this->config->item('default_language')) ? '' : $lang . "/";

		$total_segments = $this->uri->total_segments();
		@$id = ($id) ? $id : $this->db->get_where(POSTS_DB_TABLE, array('slug' => $this->uri->segment($total_segments), 'module' => PUBLISH_M, 'lang' => $lang))->row()->id;

		if($id) {
			// Get post data
			$post = $this->db->select(
								POSTS_DB_TABLE.'.*,
								'.USERS_DB_TABLE.'.name AS author_name,
								'.USERS_DB_TABLE.'.username AS author_username')
							  ->join(USERS_DB_TABLE, USERS_DB_TABLE.'.id = '.POSTS_DB_TABLE.'.author')
	 						  ->get_where(POSTS_DB_TABLE, array(POSTS_DB_TABLE.'.slug' => $this->uri->segment($total_segments), POSTS_DB_TABLE.'.lang' => $lang, POSTS_DB_TABLE.'.module' => PUBLISH_M))
	 						  ->row();

			// Get categories data
			$category = $this->db->select('title,tpl,slug,position,level')->get_where(CATEGORIES_DB_TABLE, array('id' => $post->category))->row();
			$root_cat_permalink = '';
			@$cat_position = explode('.', $category->position);

			if(sizeof($cat_position) > 1) {
				$root_cat_permalink = $this->db->select('slug')->get_where(CATEGORIES_DB_TABLE, array('position' => $cat_position[0]))->row()->slug;
				$root_cat_permalink = $root_cat_permalink . '/';
			}
			$category->permalink = $this->config->item('base_url') . $lang_url . $root_cat_permalink . $category->slug;

	 		// Get images
	 		$images = $this->db->select(FILES_DB_TABLE.'.name,'.FILES_DB_TABLE.'.title,'.FILES_DB_TABLE.'.alt')
	 				  ->order_by('position', 'asc')
	 				  ->where(array(FILES_DB_TABLE.'.item_id' => $id, FILES_DB_TABLE.'.module' => 3, FILES_DB_TABLE.'.status' => 1))
	 				  ->get(FILES_DB_TABLE)
	 				  ->result();

			return array(
				'post' => $post,
				'category' => $category,
				'images' => $images,
			);
		}
	}

	/**
	 * Checking if slug belongs to category
	 */	
	function check_category($cat_slug) {
		$sub_cat_position = '';

		$lang = $this->config->item('language_abbr');
		
		// get root category position
		$root_cat_position = $this->db->get_where(CATEGORIES_DB_TABLE, array('slug' => $this->uri->segment(1), 'lang' => $lang))->row();
		$root_cat_position = ($root_cat_position) ? $root_cat_position->position : 0;

		// check if category with $cat_slug url exists
		$sub_cats = $this->db->get_where(CATEGORIES_DB_TABLE, array('slug' => $cat_slug, 'status' => 1, 'lang' => $lang))->row();

		// if the category exists, explode the position field
		if ($sub_cats) {
			$sub_cat_position = explode('.', $sub_cats->position);
		}

		// if subcategory position has more then 1 character and first char is the same as root category position
		// return true (will then be used to load the category archive template)
		if (sizeof($sub_cat_position) > 1 AND $sub_cat_position[0] == $root_cat_position) {
			return TRUE;
		}
	}	
}
?>