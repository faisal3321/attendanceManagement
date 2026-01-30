<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


// BACKEND API ENDPOINTS

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
    // Delete worker 
    $routes->delete('workers/delete/(:num)', 'WorkerController::delete/$1');
    // Update worker
    $routes->put('workers/update/(:num)', 'WorkerController::update/$1');

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


// FRONTEND API ENDPOINTS

// CUSTOMER
$routes->get('customer/register', 'Home::register');
$routes->get('customer/login', 'Home::login');
$routes->get('customer/bookWorker', 'Home::booking');
$routes->get('customer/dashboard', 'Home::dashboard');
$routes->get('customer/attendance', 'Home::attendanceCust');
$routes->get('api/workers', 'WorkerController::index');
$routes->get('api/attendance/customer/(:any)', 'AttendanceController::showByCustomer/$1');

// ADMIN
$routes->get('admin/login', 'Home::adminLogin');
$routes->get('admin/dashboard', 'Home::adminDashboard');
$routes->get('admin/addWorker', 'Home::adminAddWorker');
$routes->get('admin/workerList', 'Home::adminWorkerList');
$routes->get('admin/attendance', 'Home::adminAttendance');
$routes->get('api/workers', 'WorkerController::index');
$routes->get('admin/workers/edit/(:num)', 'Home::editWorker/$1');