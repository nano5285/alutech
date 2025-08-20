<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Each admin form has three save buttons
 * This function defines where to redirect based on the clicked button
 */
function admin_redirect($path = '', $id = '') {	
	$CI =& get_instance();
	
	$btn = $CI->input->post('save');
	if ( $btn == 'save_new' ) {
		// If save_new button is clicked, redirect to blank form
		return redirect($CI->config->item('base_url') . ADMIN_URL . '/' . $path . '/create');
	} else if( $btn == 'save_edit' ) {
		// If save_edit button is clicked, redirect to edit form for the currently saved item
		return redirect($CI->config->item('base_url') . ADMIN_URL . '/' . $path . '/edit/' . $id);
	} else {
		// Else redirect to the list of items
		return redirect($CI->config->item('base_url') . ADMIN_URL . '/' . $path);
	}
}