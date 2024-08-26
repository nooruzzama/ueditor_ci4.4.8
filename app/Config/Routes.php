<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/*Ueditor Controller BOF*/
$routes->match(['get', 'post'], 'ueditor', 'Ueditor::index');
$routes->post('ueditor/getfiles', 'Ueditor::getfiles');
$routes->post('ueditor/getfiles', 'Ueditor::getfiles');
/*Ueditor Controller EOF*/