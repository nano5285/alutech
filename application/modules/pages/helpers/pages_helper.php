<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Homepage
 * 
 * This function will return true (1) if current page is the homepage
 */
function is_homepage() {
	$CI =& get_instance();

	if(!$CI->uri->segment(1)) {
		return true;
	}
}

/**
 * Homepage link/url
 * 
 * This function will return the homepage URL based on the currently active language
 * Usage: <?php echo homepage_link(); ?>
 */
function homepage_link() {
	$CI =& get_instance();
	// Active language
	$lang = $CI->config->item('language_abbr');
	
	// Checking if current language is set as default language
	$url = ($lang == $CI->config->item('default_language')) ? base_url() : base_url() . $lang;
	
	return $url;
}

/**
 * Check language
 * 
 * This function will return true if provided language/parameter is the same as currently active language
 */
function is_lang($lang = '') {
	$CI =& get_instance();

	if($lang == $CI->config->item('language_abbr')) {
		return true;
	}
}

/**
 * List available languages
 * 
 * Parameters
 * layout: type of output (li, img, a). By default, list is set to "a". Clean href links
 * title: If set to "code", language titles will be shorten to abbrevation (en, de, fr...)
 * 
 * Usage: <?php echo list_languages(array('layout' => 'li', 'title' => 'code')); ?>
 */
function list_languages($options = array()) {
	$CI =& get_instance();
	$default_language = $CI->config->item('default_language');
	$current_language = $CI->config->item('language_abbr');

	foreach ($CI->config->item('lang_desc') as $value=>$key) {
		// If language is the same as default language, don't show the abbrevation
		$url = ($value == $default_language) ? '' : $value;
		$title = (isset($options['title']) AND $options['title'] == 'code') ? $value : $key;

		// Set active class on active language
		$active = ($value == $current_language) ? ' active' : '';
		
		if (isset($options['layout']) AND $options['layout'] == 'option') { 
			if ($active) {
				$active = ' selected="selected"';
			}
			echo '<option value="'.base_url() . $url.'"'.@$active.'class="'.$value.'">'.$title.'</option>';
		} elseif (isset($options['layout']) AND $options['layout'] == 'li') {
			echo '<li class="'.$value.$active.'"><a href="'.base_url() . $url.'" title="'.$key.'">'.$title.'</a></li>';
		} elseif (isset($options['layout']) AND $options['layout'] == 'img') {
			$image_path = (isset($options['image_path'])) ? $options['image_path'] : 'images';
			echo '<a href="'.base_url() . $url.'" class="'.$value.$active.'" title="'.$key.'"><img src="'.base_url() . $image_path . "/" . $value.'.png" alt="'.$key.'" /></a>';
		} else {
			echo '<a href="'.base_url() . $url.'" class="'.$value.$active.'" title="'.$key.'">'.$title.'</a>';
		}
	}
}

/**
 * Breadcrumbs
 * 
 * Parameters
 * delimiter: can be whatever. By default it is set to arrow ( &gt; )
 * title: title to show
 * 
 * Usage: <?php echo breadcrumbs(array('delimiter' => ' > ', 'title' => $bc)); ?>
 */
function breadcrumbs($options = array()) {
	$CI =& get_instance();
	$output = '';
	$current_language = $CI->config->item('language_abbr');
	$options['delimiter'] = (isset($options['delimiter'])) ? $options['delimiter'] : ' &gt; ';

	// Homepage title
	$homepage_title = $CI->db->get_where(POSTS_DB_TABLE, array('slug' => '/', 'lang' => $current_language, 'module' => 1))->row()->title;	
	$output .= ($CI->uri->total_segments() > 0) ? '<a href="'.homepage_link().'">'.$homepage_title.'</a>' . $options['delimiter'] : $homepage_title;

	// Append current page title
	if(isset($options['title']) AND $options['title']) {
		$total_items = sizeof($options['title'])-1;
		for($i=0; $i<=$total_items; $i++) {
			$output .= $options['title'][$i];
			if($i != $total_items) {
				$output .= $options['delimiter'];
			}
		}
	}

	return $output;
}

/**
 * Returns configured media path
 */
function media_path($options = array()) {
	$CI =& get_instance();

	$options = array_merge(array(
		'base_url' => TRUE,
		'uploads_folder' => $CI->config->item('upload_folder'),
		'file' => '',
	), $options);

	$base_url = ( $options['base_url'] == TRUE ) ? $CI->config->item('base_url') : $options['base_url'];

	return $base_url . $options['uploads_folder'] . $options['file'];
}