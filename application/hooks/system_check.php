<?php

/**
 * This hook will scan the modules and 
 * automatically load the module helper if available
 */
class System_Check {
	function check_loaded_modules() {
		$CI =& get_instance();
		$modules_path = APPPATH.'modules/';
		$modules = $CI->config->item('cms_modules');
		//$modules = scandir($modules_path);

		foreach($modules as $module) {
			if(file_exists($modules_path . '/' . $module) AND file_exists($modules_path . '/' . $module . '/helpers/'.$module.'_helper.php')) {
				$CI->load->helper($module . '/' . $module);
			}
		}
	}
}