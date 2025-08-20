<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Image helper
 *
 * This function will resize and store the provided image on the fly.
 * New image is generated only if the same image size does not already exist (with the same name and in the same folder)
 *
 */
function image($options = array()) {
	//Get the Codeigniter object by reference
	$CI = & get_instance();
  
	$options = array_merge(array(
		'file' => '',
		'width' => 0,
		'height' => 0,
	), $options);

	$uploads_folder = $CI->config->item('upload_folder');
	$image_name = $options['file'];
	$width = $options['width'];
	$height = $options['height'];

	//The new generated filename
	$source_image = $uploads_folder . $image_name;
	$dir_name = $uploads_folder . $width . '_' . $height . '/';
	
	// Create dir for each new dimension
	if (!file_exists($dir_name) AND file_exists($source_image)) {mkdir($dir_name, 0755);}
	
	$new_image_path = $dir_name  . $width . '_' . $height . '_' . $image_name;
	 
	//The first time the image is requested
	//Or the original image is newer than our cache image
	if (!file_exists($new_image_path) AND file_exists($source_image)) {
		$CI->load->library('image_lib');
		
		//The original sizes
		$original_size = getimagesize($source_image);
		$original_width = $original_size[0];
		$original_height = $original_size[1];
		$ratio = $original_width / $original_height;
		
		//The requested sizes
		$requested_width = $width;
		$requested_height = $height;
		
		//Initialising
		$new_width = 0;
		$new_height = 0;
		
		//Calculations
		if ($requested_width > $requested_height) {
			$new_width = $requested_width;
			$new_height = $new_width / $ratio;
			if ($requested_height == 0)
				$requested_height = $new_height;
			
			if ($new_height < $requested_height) {
				$new_height = $requested_height;
				$new_width = $new_height * $ratio;
			}
		
		} else {
			$new_height = $requested_height;
			$new_width = $new_height * $ratio;
			if ($requested_width == 0)
				$requested_width = $new_width;
			
			if ($new_width < $requested_width) {
				$new_width = $requested_width;
				$new_height = $new_width / $ratio;
			}
		}
		
		$new_width = ceil($new_width);
		$new_height = ceil($new_height);
		
		//Resizing
		$config = array();
		$config['image_library'] = 'gd2';
		$config['source_image'] = $source_image;
		$config['new_image'] = $new_image_path;
		$config['maintain_ratio'] = FALSE;
		$config['height'] = $new_height;
		$config['width'] = $new_width;
		$CI->image_lib->initialize($config);
		$CI->image_lib->resize();
		$CI->image_lib->clear();
		
		//Crop if both width and height are not zero
		if (($width != 0) && ($height != 0)) {
			$x_axis = floor(($new_width - $width) / 2);
			$y_axis = floor(($new_height - $height) / 2);
			
			//Cropping
			$config = array();
			$config['source_image'] = $new_image_path;
			$config['maintain_ratio'] = FALSE;
			$config['new_image'] = $new_image_path;
			$config['width'] = $width;
			$config['height'] = $height;
			$config['x_axis'] = $x_axis;
			$config['y_axis'] = $y_axis;
			$CI->image_lib->initialize($config);
			$CI->image_lib->crop();
			$CI->image_lib->clear();
		}
	}
	 
	return base_url() . $new_image_path;
}

/* End of file image_helper.php */