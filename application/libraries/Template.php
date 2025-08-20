<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template {
		var $template_data = array();
		
		function set($name, $value) {
			$this->template_data[$name] = $value;
		}
	
		function load($template = '', $view = '' , $view_data = array(), $return = FALSE) {               
			$this->CI =& get_instance();
			$this->set('content', $this->CI->load->view($view, $view_data, TRUE));			
			return $this->CI->load->view($template, $this->template_data, $return);			
		}
		
		function load_partial($template = '', $view = '' , $view_data = array(), $return = FALSE) {
			$this->set('content', $this->template_data['controller']->load->view($view, $view_data, TRUE));
			return $this->template_data['controller']->load->view($template, $this->template_data, $return);
		}
		
		function load_include($template = '', $area = '',  $view = '' , $view_data = array(), $return = FALSE) {
			$this->set($area, $this->template_data['controller']->load->view($view, $view_data, TRUE));
			return $this->template_data['controller']->load->view($template, $this->template_data, $return);
		} 
}

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */