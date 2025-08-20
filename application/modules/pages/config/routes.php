<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route[ADMIN_URL . '/pages'] = 'pages/admin';
$route[ADMIN_URL . '/pages/(:num)'] = 'pages/admin/index/$1';
$route[ADMIN_URL . '/pages/create'] = 'pages/admin/create';
$route[ADMIN_URL . '/pages/edit/(:num)'] = 'pages/admin/edit/$1';
$route[ADMIN_URL . '/pages/delete/(:num)'] = 'pages/admin/delete/$1';