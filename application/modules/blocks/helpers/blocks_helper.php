<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Blocks
 */
function block($identifier = '', $default = FALSE) {
	$CI =& get_instance();
	$lang = ($default == FALSE) ? $CI->config->item('language_abbr') : $CI->config->item('default_language');
	$slug = ($CI->uri->segment(1) == '') ? "/" : $CI->uri->segment(1);
	$data = $CI->db->get_where(POST_META_DB_TABLE, array('identifier' => $identifier, 'lang' => $lang, 'module' => 0));

	if ($data->num_rows() > 0) {
		return $data->row()->content;
	}
}

/**
 * Custom Blocks / Post Meta
 */
function custom_block($identifier = '', $post_id) {
	$CI =& get_instance();
	$lang = $CI->config->item('language_abbr');
	$data = $CI->db->get_where(POST_META_DB_TABLE, array('identifier' => $identifier, 'lang' => $lang, 'post_id' => $post_id));

	if ($data->num_rows() > 0) {
		return $data->row()->content;
	}
}