<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route[ADMIN_URL . '/publish/category'] = 'publish/admin_categories';
$route[ADMIN_URL . '/publish/category/(:num)'] = 'publish/admin_categories/index/$1';
$route[ADMIN_URL . '/publish/category/create'] = 'publish/admin_categories/create';
$route[ADMIN_URL . '/publish/category/edit/(:num)'] = 'publish/admin_categories/edit/$1';
$route[ADMIN_URL . '/publish/category/delete/(:num)'] = 'publish/admin_categories/delete/$1';

$route[ADMIN_URL . '/publish/posts'] = 'publish/admin';
$route[ADMIN_URL . '/publish/posts/(:num)'] = 'publish/admin/index/$1';
$route[ADMIN_URL . '/publish/posts/create'] = 'publish/admin/create';
$route[ADMIN_URL . '/publish/posts/edit/(:num)'] = 'publish/admin/edit/$1';
$route[ADMIN_URL . '/publish/posts/delete/(:num)'] = 'publish/admin/delete/$1';
$route['get_admin_category_tree'] = 'publish/admin/get_admin_category_tree';

$route['pravne-napomene|en/news|en/gallery|novosti'] = 'publish';
$route['pravne-napomene|en/news|en/gallery|novosti(:any)'] = 'publish/$1';