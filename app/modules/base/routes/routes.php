<?php
use \Devvoh\Fluid\App as App;

$routes[] = array(
    'method' => 'GET',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
);
$routes[] = array(
    'method' => 'GET',
    'path' => '/test',
    'controller' => 'home',
    'action' => 'test',
);
$routes[] = array(
    'method' => 'GET',
    'path' => '/user/i:id',
    'controller' => 'home',
    'action' => 'viewUser',
);
$routes[] = array(
    'method' => 'GET|POST',
    'path' => '/user/a:name',
    'view' => 'closure/index',
    'closure' => function() {
        $user = \Devvoh\Fluid\App::getParam()->get('name');
        \Devvoh\Fluid\App::getParam()->set('hello', 'Hello, '.$user.'!');
    },
);

// Add module to all routes
foreach ($routes as &$route) {
    $route['module'] = 'base';
}
App::getRouter()->addRoutes($routes);