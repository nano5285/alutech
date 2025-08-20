<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function contact_form($options = array()) {
	$CI =& get_instance();
	$CI->load->library('form_validation');
	$CI->load->language('english');

	$options = array_merge(array(
		'mailto' => $CI->config->item('base_email'),
		'form_template' => 'forms/contact_form',
		'success_template' => 'forms/contact_success'
	), $options);
	
	if (!$CI->input->post('submit')) {
		$CI->load->view($options['form_template']);
	} else {
		#get input values from the view file
		$name = $CI->input->post('name');
		$email = $CI->input->post('email');
		$subject = $CI->input->post('subject');
		$message = $CI->input->post('message');
		$secure_code = $CI->input->post('secure_code');
		
		#validation
		$CI->form_validation->set_rules('secure_code', 'required');
		$CI->form_validation->set_rules('name', '', 'trim|required|min_length[2]');
		$CI->form_validation->set_rules('email', '', 'trim|required|valid_email');
		$CI->form_validation->set_rules('subject', '', 'trim|required|min_length[2]');
		$CI->form_validation->set_rules('message', '', 'trim|required|min_length[2]');
		$CI->form_validation->set_error_delimiters('<span class="error">', '</span>');
		
		if ($CI->form_validation->run() == FALSE) {
			$CI->load->view($options['form_template']);
		} else {
			if ($secure_code == "siteform") {
				$CI->load->library('email');
				$CI->email->from($email, $name);
				$CI->email->to($options['mailto']);
				$CI->email->subject($subject);
				$CI->email->message($message);
				
				$CI->email->send();
				
				$CI->load->view($options['success_template']);
			} else {
				redirect($CI->config->item('base_url'));
			}
		}		
	}
}