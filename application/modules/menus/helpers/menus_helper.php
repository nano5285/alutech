<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CMS menus
 * 
 * Available parameters:
 * identifier
 * layout (ul, a)
 * menu_attr
 * link (true, false)
 * item_class
 * item_id
 * before_item
 * after_item
 * active_class
 *	
 * Usage: <?php echo menu(array('identifier' => 'main', 'layout' => 'ul', 'item_class' => 'item')); ?>
 */
function menu($options = array()) {
	$CI =& get_instance();

	// Default values
	$options = array_merge(array(
		'table' => MENU_ITEMS_DB_TABLE, 
		'identifier' => '',
		'status' => 1,
		'menu_attr' => '',
		'layout' => 'ul', 
		'link' => true, 
		'item_class' => '', 
		'item_id' => '',
		'before_item' => '', 
		'after_item' => '',
		'active_class' => 'active',
		'lang' => $CI->config->item('language_abbr')), $options);

	// Get menu id based on identifier
	if(isset($options['identifier'])) {
		$menu_query = $CI->db->get_where('menus', array('identifier' => $options['identifier'], 'lang' => $options['lang']));
		if ($menu_query->num_rows() > 0)
			@$options['menu_id'] = $menu_query->row()->id;
	}

	// Define where clause
	$qualificationArray = array('menu_id', 'parent');
	foreach($qualificationArray as $qualifier) {
		if(isset($options[$qualifier])) $CI->db->where($qualifier, $options[$qualifier]);
	}

	// Check if menu exists
	if ( isset($options['menu_id']) ) {	
		// Get menu items
		$result = $CI->db->where('status', 1)->order_by('position', 'asc')->get($options['table']);

		$menu_data = array(
			'items' => array(),
			'parents' => array()
		);

		// Loop through and set parents and subitems arrays
		foreach($result->result_array() as $menu_item) {
			$menu_data['items'][$menu_item['id']] = $menu_item; 
			$menu_data['parents'][$menu_item['parent']][] = $menu_item['id']; 
		}	

		// Menu builder function, parent 0 is the root 
		return build_menu(
						0,
						$menu_data, 
						" " . $options['menu_attr'], 
						$options['link'], 
						$options['before_item'], 
						$options['after_item'], 
						$options['item_class'], 
						$options['item_id'], 
						$options['layout'], 
						$options['active_class']
					  );
	}
}

/**
 * Menu builder
 */
function build_menu($parent, $menu_data, $menu_attr, $link, $before_item, $after_item, $item_class, $menu_item_id, $layout, $active_class) { 
	$CI =& get_instance();
	$html = ''; 
	$target = '';
	$active = '';
	$class = '';
	$id = '';
	$default_language = $CI->config->item('default_language');
	$current_language = $CI->config->item('language_abbr');

	/**
	 * Setting items URL and lang prefix
	 * If item is already part of the default language, ignore the URL lang prefix
	 */	
	if ($current_language == $default_language) {
		$lang_url = '';
		$homepage_url = '';
		$page_url = ( $CI->uri->segment(1) == '' ) ? '/' : $CI->uri->segment(1);
	} else {
		$lang_url = $current_language . '/';
		$homepage_url = $CI->config->item('language_abbr');
		$page_url = ( $CI->uri->segment(1) == '' ) ? $current_language : $lang_url . $CI->uri->segment(1);
	}

	if ($layout == 'a') {
		foreach ($menu_data['items'] as $item) { 
			// If url with http is entered
			if (substr($item['url'], 0, 7) == "http://" || substr($item['url'], 0, 8) == "https://")  {
				$item_url = $item['url'];
			} else {
				// If homepage URL
				if($item['url'] == "/") {
					$item_url = base_url() . $homepage_url;
				} else {
					$item_url = base_url() . $lang_url . $item['url'];
				}
			}
			// If target blank is checked
			if ($item['target'] == 1) {
				$target = ' target="_blank"';
			}

			// If class is defined, add class to the li element
			$class = '';

			if($CI->uri->segment(1) == '') {
				$class .= ($item['url'] == '/') ? $active_class : '';
			} else {
				$class .= ($page_url == $lang_url . $item['url']) ? $active_class : '';	
			}

			$class .= ($item_class) ? " " . $item_class . " " . $item_class.$item['id'] : '';

			$class = ($class) ? ' class="'.trim($class).'"' : '';

			if($menu_item_id) {
				$id = ' id="'.$menu_item_id.$item['id'].'"';
			}

			// Output href
			$html .= '<a href="'.$item_url.'"'.$class.$id.$target.'>'; 

			// If before is defined
			if($before_item) {
				$html .= $before_item;
			}

			$html .= $item['title'];

			// If after is true
			if($after_item) {
				$html .= $after_item;
			}

			$html .= '</a>' . "\n";
		}
	} else {
		if (isset($menu_data['parents'][$parent])) {
			// If menu attributes are defined
			if($menu_attr && $parent == 0) {
				$html = '<'.$layout.$menu_attr.'>' . "\n"; 
			} else {
				$html = '<'.$layout.'>' . "\n";
			}

			foreach ($menu_data['parents'][$parent] as $item_id) { 
				$class = '';

				// Append class if item has any children
				$class .= ( isset($menu_data['parents'][$item_id]) ) ? 'has-children ' : '';

				// If page url is the same as item url, append active class
				if($CI->uri->segment(1) == '') {
					$class .= ($menu_data['items'][$item_id]['url'] == '/') ? $active_class : '';
				} else {
					$class .= ($page_url == $lang_url . $menu_data['items'][$item_id]['url']) ? $active_class : '';	
				}

				// Append custom item classes if defined
				$class .= ($item_class) ? " " . $item_class . " " . $item_class.$menu_data['items'][$item_id]['id'] : '';
				
				// Append item id if defined	
				$id = ($menu_item_id) ? ' id="'.$menu_item_id.$menu_data['items'][$item_id]['id'].'"' : '';

				$class = ($class) ? 'class="'.trim($class).'"' : '';
				$html .= '<li '.$class.$id.'>'; 

				// Add or remove system base URL based on the item url. If http of https prefix is found, base url will be ignored
				if (substr($menu_data['items'][$item_id]['url'], 0, 7) == "http://" || substr($menu_data['items'][$item_id]['url'], 0, 8) == "https://") {
					$item_url = $menu_data['items'][$item_id]['url'];
				} else {
					$item_url = ($menu_data['items'][$item_id]['url'] == "/") ? base_url() . $homepage_url : base_url() . $lang_url . $menu_data['items'][$item_id]['url'];
				}

				// If target blank is checked
				if ($menu_data['items'][$item_id]['target'] == 1) {
					$target = ' target="_blank"';
				}

				// If link is true
				if($link) {
					$html .= '<a href="'.$item_url.'"'.$target.'>';
				}

				// If before is set
				if($before_item) {
					$html .= $before_item;
				}

				$html .= $menu_data['items'][$item_id]['title'];

				// If after is set
				if($after_item) {
					$html .= $after_item;
				}

				// If link is true
				if($link) {
					$html .= '</a>';
				}

				// Find childitems recursively 
				$html .= build_menu($item_id, $menu_data, $menu_attr, $link, $before_item, $after_item, $item_class, $menu_item_id, $layout, $active_class); 
	
				$html .= '</li>' . "\n";
			} 
			
			$html .= '</'.$layout.'>';
		} 
	}
	return $html; 
}