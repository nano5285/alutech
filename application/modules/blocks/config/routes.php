<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route[ADMIN_URL . '/blocks'] = 'blocks/admin';
$route[ADMIN_URL . '/blocks/(:num)'] = "blocks/admin/index/$1";
$route[ADMIN_URL . '/blocks/create'] = "blocks/admin/create";
$route[ADMIN_URL . '/blocks/edit/(:num)'] = "blocks/admin/edit/$1";
$route[ADMIN_URL . '/blocks/delete/(:num)'] = 'blocks/admin/delete/$1';