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
    'closure' => function() {
        echo 'Closure controller for user: ' . App::getParam()->getValue('name');
    },
);

App::getRouter()->addRoutes('base', $routes);