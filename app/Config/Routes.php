<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// restful api routes
$routes->group('api', function($routes) {

    // super admin
    $routes->post('super/admin', 'SuperAdminController::setupSuperAdmin');

    // admin
    $routes->post('admin', 'SuperAdminController::createAdmin');
    // login
    $routes->post('login', 'SuperAdminController::login');

    
    // add-worker
    $routes->post('add/worker', 'WorkerController::create');


    // calendar 
    $routes->get('calendar', 'CalendarController::index');


    // Customer Endpoints
    $routes->group('customer', function($routes) {
        $routes->post('register', 'CustomerController::register');
        $routes->post('login', 'CustomerController::login');
    });


    // Booking endpoints
    $routes->get('bookings', 'BookingController::index');
    $routes->post('book/worker', 'BookingController::create');
    // GET: /api/mybookings/CUST1234509823
    $routes->get('mybookings/(:any)', 'BookingController::show/$1');


    // MAIN ATTENDANCE API
    $routes->get('attendance', 'AttendanceController::index');
    $routes->post('attendance/sync', 'AttendanceController::syncDailyAttendance');
    $routes->post('attendance/customer/update', 'AttendanceController::updateCustomerStatus');
    $routes->put('attendance/admin/override', 'AttendanceController::adminOverride');

});