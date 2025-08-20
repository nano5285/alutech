<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

function form_wysiwyg($name = 'text', $textarea = true, $content = '', $tbar = 'full') {
	$CI = & get_instance();
	
	$config = array();
	$config['filebrowserBrowseUrl'] = $CI->config->item('admin_assets_path')."kcfinder/browse.php";
	$config['filebrowserUploadUrl'] = $CI->config->item('admin_assets_path')."kcfinder/browse.php";

	// Load editor plugins based on choosen type
	if($tbar == 'full') {
		$config['toolbar'] = '[["Source","-","Bold","Italic","Underline","Strike","Subscript","Superscript","RemoveFormat","-","Cut","Copy","Paste","PasteText","PasteFromWord","-","Undo","Redo"],["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock","NumberedList","BulletedList","-","Blockquote"],["Flash","Image","Link","Unlink","Anchor","Table","HorizontalRule","SpecialChar","-","Styles","Format"]]';
	} else {
		$config['toolbar'] = '[["Source","-","Bold","Italic","Underline","Strike","Subscript","Superscript","RemoveFormat","-","Paste","PasteText","PasteFromWord"],["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"]]';
	}

	$config['language'] = $CI->config->item('wysiwyg_lang');
	$config['defaultLanguage'] = $CI->config->item('wysiwyg_lang');
	$config['stylesSet'] = "my_styles:".base_url().$CI->config->item('styles_set');
	$config['contentsCss'] = base_url().$CI->config->item('contents_css');
	$config['skin'] = 'bootstrapck';
	$config['extraAllowedContent'] = 'iframe[*]';
	//$config['width'] = '100%';
	//$config['height'] = '300px';

	// Format config items
	$cfg = '';
	$total_cfg_items = sizeof($config);
	$i = 1;
	foreach($config as $key => $value) {
		$cfg .= '"' . $key . '"' . ':';
		$cfg .= ($key === 'toolbar') ? $value : '"' . $value . '"';
		$cfg .= ($i != $total_cfg_items) ? ',' : '';
		$i++;
	}

	// Output textarea
	if ($textarea == true) {
		echo '<textarea name="'.$name.'" cols="30" rows="10">'.$content.'</textarea>' . "\n";
	}

	// Output script
	$out = '<script src="'.$CI->config->item('admin_assets_path').'ckeditor/ckeditor.js?t='.date('ymd').time().'"></script>' . "\n";
	$out .= "<script>";
	$out .= "var cfg = {".$cfg."}" . "\n";
	if ($textarea == true)
		$out .= 'CKEDITOR.replace("'.$name.'", cfg);';
	$out .= "</script>\n";
	
	return $out;
}
