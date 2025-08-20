<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

#MENUS
$route[ADMIN_URL . '/menus'] = 'menus/admin';
$route[ADMIN_URL . '/menus/(:num)'] = "menus/admin/index/$1";
$route[ADMIN_URL . '/menus/create'] = "menus/admin/create";
$route[ADMIN_URL . '/menus/edit/(:num)'] = "menus/admin/edit/$1";
$route[ADMIN_URL . '/menus/delete/(:num)'] = 'menus/admin/delete/$1';

#MENU ITEMS
$route[ADMIN_URL . '/menuitems/(:num)'] = 'menus/menuitems/index/$1';
$route[ADMIN_URL . '/menuitems/reorder/(:num)'] = 'menus/menuitems/reorder/$1';
$route['reorder_menuitems'] = 'menus/menuitems/reorder_menuitems';