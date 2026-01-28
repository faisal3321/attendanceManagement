<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// restful api routes
$routes->group('api', function($routes) {
    // super admin
    $routes->post('setup-super-admin', 'SuperAdminController::setupSuperAdmin');

    // admin
    $routes->post('admin', 'SuperAdminController::createAdmin');

    // login
    $routes->post('login', 'SuperAdminController::login');

    // add-worker
    $routes->post('add-worker', 'WorkerController::create');

    // calendar 
    $routes->get('calendar', 'CalendarController::index');

});