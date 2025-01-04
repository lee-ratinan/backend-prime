<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
// Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginScript');
$routes->post('update-expired-password', 'Auth::updateExpiredPassword');
$routes->post('resend-otp', 'Auth::resendOTP');
$routes->post('verify-otp', 'Auth::verifyOTP');
$routes->post('google-signin', 'Auth::loginGoogle');
// Forgot Password
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::forgotPasswordScript');
// Reset Password (click from email)
$routes->get('reset-password/(:any)/(:any)', 'Auth::resetPassword/$1/$2');
$routes->post('reset-password', 'Auth::resetPasswordScript');
// Register - reserved for future use
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerScript');
// Logout
$routes->get('logout', 'Auth::logout');
// Files
$routes->get('file/(:any)', 'File::index/$1/0');
$routes->get('download/(:any)', 'File::index/$1/1');
// Cron
$routes->get('cron/run-monthly', 'Cron::runMonthly');
// SYSTEM
$routes->group('{locale}/office', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Office::index');
    $routes->get('dashboard', 'Office::index');
    $routes->get('profile', 'Office::profile');
    $routes->post('profile', 'Office::profileScript');
    $routes->get('switch-role', 'Office::switchRole');
    $routes->post('switch-role', 'Office::switchRoleScript');
    // User
    $routes->get('user', 'User::index');
    $routes->post('user', 'User::list');
    $routes->get('user/create', 'User::edit/new');
    $routes->get('user/edit/(:any)', 'User::edit/$1');
    $routes->post('user/edit', 'User::editScript');
    $routes->get('public-profile/(:any)', 'User::publicProfile/$1');
    // Role
    $routes->get('role', 'Role::index');
    $routes->post('role', 'Role::list');
    $routes->get('role/create', 'Role::edit/new-role');
    $routes->get('role/edit/(:any)', 'Role::edit/$1');
    $routes->post('role/edit', 'Role::editScript');
    $routes->get('role/feature', 'Role::feature');
    // Organization
    $routes->get('organization', 'Organization::index');
    $routes->post('organization', 'Organization::update');
    // Log
    $routes->get('log', 'Log::index');
    $routes->post('log', 'Log::list');
    $routes->get('log/email', 'Log::email');
    $routes->post('log/email', 'Log::emailList');
    $routes->get('log/log-file', 'Log::fileList');
    $routes->get('log/log-file/(:any)', 'Log::fileView/$1');
});
