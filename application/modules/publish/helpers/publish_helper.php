<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_category_tree($lang, $level = 0, $cat) {
	$CI =& get_instance();
	$CI->adminmodel->get_admin_category_tree($lang, $level, $cat);
}

function get_category_url($url, $level = 1) {
	$CI =& get_instance();

	// get current language abbreviation
	$current_lang = $CI->config->item('language_abbr');

	// if current language is the same as the default one, ignore the abbreviation
	$lang = ($current_lang == $CI->config->item('default_language')) ? '' : $current_lang . "/";

	$url = ($level == 1) ? $url : $CI->uri->segment(1) .'/' . $url;
	return $CI->config->item('base_url') . $lang . $url;
}

function get_categories() {
	$CI =& get_instance();

	$query = $CI->db->get('categories');
}

/**
 * Category tree
 */
function category_tree($options = array()) {
	$CI =& get_instance();

	// default values
	$options = array_merge(array(
		'status' => 1,
		'list_attr' => '',
		'link' => true, 
		'exclude' => '',
		'item_class' => '',
		'item_id' => '',
		'before_item' => '', 
		'after_item' => '',
		'active_class' => 'selected',
		'lang' => $CI->config->item('language_abbr')), $options);

	// exclude categories with specified slug/url
	if(isset($options['exclude']) AND $options['exclude']) {
		$CI->db->where_not_in('slug', $options['exclude']);
	}

	$CI->db->where(array('status' => $options['status'], 'lang' => $options['lang']));

	if(isset($options['start_position'])) {
		$start_position = explode('.', $options['start_position']);
		$start_position = (sizeof($start_position)) ? $start_position[0] : $start_position;
		$CI->db->where('position >=', $start_position);
	}

	if(isset($options['end_position'])) {
		$CI->db->where('position <', $options['end_position']);
	}

	$CI->db->order_by('position', 'asc');
	$result = $CI->db->get(CATEGORIES_DB_TABLE);

	// items and parents arrays
	$category_data = array(
		'items' => array(),
		'parents' => array()
	);

	// set parents and items arrays
	foreach($result->result_array() as $category_item) {
		$category_data['items'][$category_item['id']] = $category_item;
		$category_data['parents'][$category_item['parent']][] = $category_item['id'];
	}

	// menu builder function, parent 0 is the root 
	return build_category_tree(
					0,
					$category_data, 
					" " . $options['list_attr'], 
					$options['link'], 
					$options['before_item'], 
					$options['after_item'], 
					$options['item_class'],
					$options['exclude'],
					$options['item_id'], 
					$options['active_class']
				  );
}

// menu builder function
function build_category_tree($parent, $category_data, $list_attr, $link, $before_item, $after_item, $item_class, $exclude, $category_item_id, $active_class) { 
	$CI =& get_instance();
	$html = ''; 
	$active = '';
	$class = '';
	$id = '';
	$exclude = array();
	$default_language = $CI->config->item('default_language');
	$current_language = $CI->config->item('language_abbr');

	if (isset($category_data['parents'][$parent])) {
		// Setting items lang prefix
		$lang = ($current_language == $default_language) ? '' : $current_language . '/'; 

		// If menu attributes are defined
		$html = ($list_attr && $parent == 0) ? '<ul'.$list_attr.'>' . "\n" : '<ul>' . "\n";

		foreach ($category_data['parents'][$parent] as $item_id) { 			
			if($category_data['items'][$item_id]['parent'] != 0) {			
				$root_position = explode('.', $category_data['items'][$item_id]['position']);

				foreach($category_data['items'] as $category_parent) {
					if($category_parent['position'] == $root_position[0]) {
						$parent_slug = $category_parent['slug'];
						break;
					}
				}
								
				$category_slug = base_url() . $lang . $parent_slug . '/' . $category_data['items'][$item_id]['slug'];
			} else {
				$category_slug = base_url() . $lang . $category_data['items'][$item_id]['slug'];
			}

			$class = '';

			// Append class if item has any children
			$class .= ( isset($category_data['parents'][$item_id]) ) ? 'has-children ' : '';

			// Append custom item classes if defined
			$class .= ($item_class) ? $item_class . ' ' . $item_class.$category_data['items'][$item_id]['id'] : '';

			// Append active item class
			if($category_slug == base_url() . $lang . $CI->uri->segment(1) OR $category_slug == base_url() . $lang . $CI->uri->segment(1) . '/' . $CI->uri->segment(2)) {
				$class .= ' ' . $active_class;
			}

			// Append item id if defined	
			$id = ($category_item_id) ? ' id="'.$category_item_id.$category_data['items'][$item_id]['id'].'"' : '';

			$class = ($class) ? 'class="'.trim($class).'"' : '';
			$html .= '<li '.$class.$id.'>'; 

			if($link) {
				$html .= '<a href="'.$category_slug.'">';
			}

			if($before_item) {
				$html .= $before_item;
			}

			$html .= $category_data['items'][$item_id]['title'];

			if($after_item) {
				$html .= $after_item;
			}

			if($link) {
				$html .= '</a>';
			}

			// Find childitems recursively 
			$html .= build_category_tree($item_id, $category_data, $list_attr, $link, $before_item, $after_item, $item_class, $exclude, $category_item_id, $active_class); 

			$html .= '</li>' . "\n";
		} 
		
		$html .= '</ul>';
	} 

	// return menu
	return $html; 
}

/**
 * Get post/s based on provided parameters
 */
function get_posts($options = array()) {
	$CI =& get_instance();
	$CI->config->load('publish/config');
	$CI->load->model('publish/publishmodel');
	$CI->load->helper('image');

	$data = $CI->publishmodel->get_posts($options);
	return $data['posts'];
}