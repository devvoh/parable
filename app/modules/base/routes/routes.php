<?php
$routes[] = array(
    'method' => 'GET|POST',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
);
$routes[] = array(
    'method' => 'GET|POST',
    'path' => '/test',
    'controller' => 'home',
    'action' => 'test',
);

Devvoh\Fluid\App::getRouter()->addRoutes('base', $routes);