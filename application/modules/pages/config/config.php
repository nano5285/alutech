<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Page Templates
 * 
 * File Name => Template Description
 */	
$config['templates'] = array(
							 'homepage' => 'Homepage Template',
							 'contact' => 'Contact Template',
							 'centar' => 'Centar',
							 'dokumenti' => 'Dokumenti',
							 'laboratorij' => 'Laboratorij',
							 'marikultura' => 'Marikultura',
							 'obradnicentar' => 'Obradni centar',
							 'konstrukcijski' => 'Konstrukcijski biro',
							 'prototipna' => 'Prototipna radionica',
							 'klasteri' => 'Centar za klastere',
							 'fondovi' => 'Ured za EU fondove',
							 'energija' => 'Centar za obnovljive izvore energije',
							 'edukacijski' => 'Edukacijski centar',
							 'usluge' => 'Usluge',
							 'pravila' => 'Pravila',
							 'projekt' => 'Projekt',
							 );

$config['pages_content'] = 'full'; // use content field with WYSIWYG support (full / simple / none / false)
$config['pages_seo'] = TRUE; // use page SEO fields

/**
 * Custom Page Fields
 * 
 * '<identifier>' => array(
 *				'name' => '<field display name>',
 *				'field_type' => '<field type>' // checkbox, text, textarea, wysiwyg, simple_wysiwyg
 *				 ),
 * 
 */	
$config['pages_custom_fields'] = array(
									'slider_1' => array(
													'title' => 'Extra content',
													'field_type' => 'wysiwyg'
													 ),
									'slider_2' => array(
													'title' => 'Extra content',
													'field_type' => 'wysiwyg'
													 ),
									'slider_3' => array(
													'title' => 'Extra content',
													'field_type' => 'wysiwyg'
													 ),
									'extra_content1' => array(
													'title' => 'Extra content',
													'field_type' => 'wysiwyg'
													 ),
									'extra_content2' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									'extra_content3' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									'extra_content3' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content4' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content5' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content6' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content7' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content8' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content9' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									 'extra_content10' => array(
													'title' => 'Extra content',
													'field_type' => 'simple_wysiwyg'
													 ),
									'map' => array(
													'title' => 'Map',
													'field_type' => 'textarea'
													 ),
									 
									 );
									 
