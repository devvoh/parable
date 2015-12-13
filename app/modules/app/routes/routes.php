<?php
use \Devvoh\Fluid\App as App;

$routes['index'] = [
    'method' => 'GET',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
];

// Add module to all routes
foreach ($routes as &$route) {
    $route['module'] = 'app';
}
App::getRouter()->addRoutes($routes);