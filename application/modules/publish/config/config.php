<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['publish_category_image'] = FALSE; // use category image (true / false)
$config['publish_category_content'] = 'full'; // use category content field with WYSIWYG support (full / simple / none / false)
$config['publish_category_publish_date'] = TRUE; // use category publish date field (true / false)
$config['publish_category_seo'] = TRUE; // use category SEO fields

$config['publish_author'] = TRUE; // use post author field
$config['publish_admin_author_column'] = FALSE; // use admin author column
$config['publish_excerpt'] = 'simple'; // use post excerpt field with WYSIWYG support (full / simple / none / false)
$config['publish_content'] = 'full'; // use post content field with WYSIWYG support (full / simple / none / false)
$config['publish_date'] = TRUE; // use publish date field (true / false)
$config['publish_images'] = TRUE; // use post image fields
$config['publish_admin_image_column'] = TRUE; // use admin image column
$config['publish_image_fields'] = 5; // number of post upload fields
$config['publish_per_page'] = 20; // posts per page
	$config['publish_gallery_per_page'] = 20; // posts per page based on category template
$config['publish_orderby'] = 'date'; // posts order by
$config['publish_order_direction'] = 'desc';
$config['publish_rss_per_page'] = 20; // rss items per page
$config['publish_featured_post'] = FALSE; // use featured post option
$config['publish_seo'] = TRUE; // use post SEO fields

/**
 *  Post Custom Fields
 * 
 * "<identifier>" => array(
 *				"name" => "<field display name>",
 *				"field_type" => "<field type>" // checkbox, text, textarea, wysiwyg, simple_wysiwyg
 *				 ),
 * 
 */	
$config['publish_custom_fields'] = array(
								"onemoguci_link" => array(
									"title" => "Disable title link or read-more link",
									"field_type" => "checkbox",
									),
								"autor_izvora" => array(
									"title" => "Source name and link",
									"field_type" => "textarea",
									),
								"dodatno_polje" => array(
									"title" => "Additional content",
									"field_type" => "textarea",
									),
								);		

/**
 * Category Templates
 * 
 * Folder => Template Description
 */	
$config['publish_templates'] = array(
							 "blog" => "Blog template",
							 "gallery" => "Gallery template",
							 );