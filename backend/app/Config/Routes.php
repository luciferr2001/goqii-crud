<?php

use App\Controllers\Masters\User;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group("v1", function ($routes) {
    // Protected Routes
    $routes->group("user", ['filter' => 'auth'], function ($routes) {
        $routes->get("form", [User::class, 'master_form_user']);
        $routes->get("(:any)", [User::class, 'detail_user/$1']);
        $routes->post("", [User::class, 'add_user']);
        $routes->patch("(:any)", [User::class, 'edit_user/$1']);
        $routes->delete("(:any)", [User::class, 'delete_user/$1']);
    });
});
