<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/authenticate', 'Auth::authenticate');
$routes->get('/logout', 'Auth::logout');

// Protected Routes (require login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/access-denied', 'Dashboard::accessDenied');

    // Customers
    $routes->get('/customers', 'Customers::index');
    $routes->get('/customers/create', 'Customers::create', ['filter' => 'rolecheck:admin']);
    $routes->post('/customers/store', 'Customers::store', ['filter' => 'rolecheck:admin']);
    $routes->get('/customers/view/(:num)', 'Customers::view/$1');
    $routes->get('/customers/edit/(:any)', 'Customers::edit/$1');
    $routes->post('/customers/update/(:num)', 'Customers::update/$1');
    $routes->get('/customers/delete/(:num)', 'Customers::delete/$1', ['filter' => 'rolecheck:admin']);
    $routes->get('/customers/export', 'Customers::export');
});

$routes->group('api', function ($routes) {

    // Authentication
    $routes->post('login', 'Api\Auth::login');

    // Customer API
    $routes->group('customers', ['filter' => 'jwt'], function ($routes) {

        $routes->get('/', 'Api\Customers::index');

        $routes->get('(:num)', 'Api\Customers::show/$1');

        $routes->post('/', 'Api\Customers::create');

        $routes->put('(:num)', 'Api\Customers::update/$1');

        $routes->delete('(:num)', 'Api\Customers::delete/$1');
    });
});
