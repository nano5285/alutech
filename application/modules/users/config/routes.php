<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route[ADMIN_URL . '/users'] = 'users/admin';
$route[ADMIN_URL . '/users/(:num)'] = "users/admin/index/$1";
$route[ADMIN_URL . '/users/create'] = "users/admin/create";
$route[ADMIN_URL . '/users/edit/(:num)'] = "users/admin/edit/$1";
$route[ADMIN_URL . '/users/delete/(:num)'] = 'users/admin/delete/$1';
$route[ADMIN_URL . '/users/delete/(:num)/(:num)'] = 'users/admin/delete/$1/$2';
$route[ADMIN_URL . '/users/profile'] = 'users/admin/profile';