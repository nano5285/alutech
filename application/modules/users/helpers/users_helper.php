<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function logged_in() {
	$CI =& get_instance();
	
	if($CI->session->userdata('logged_in')) {
		return TRUE;
	}
}