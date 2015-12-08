<?php
use \Devvoh\Fluid\App as App;

$routes['index'] = [
    'method' => 'GET',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
];
$routes['test'] = [
    'method' => 'GET',
    'path' => '/test',
    'controller' => 'home',
    'action' => 'test',
];
$routes['user-view-id'] = [
    'method' => 'GET',
    'path' => '/user/:id',
    'controller' => 'home',
    'action' => 'viewUser',
];
$routes['user-view-name'] = [
    'method' => 'GET|POST',
    'path' => '/user-name/:name',
    'view' => 'closure/index',
    'closure' => function() {
        $user = \Devvoh\Fluid\App::getParam()->get('name');
        \Devvoh\Fluid\App::getParam()->set('hello', 'Hello, '.$user.'!');
    },
];

// Add module to all routes
foreach ($routes as &$route) {
    $route['module'] = 'app';
}
App::getRouter()->addRoutes($routes);