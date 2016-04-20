<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

$app = \Devvoh\Parable\App::getInstance();

$routes['index'] = [
    'method' => 'GET',
    'path' => '/',
    'controller' => 'Home',
    'action' => 'index',
];
$routes['closure'] = [
    'method' => 'GET',
    'path' => '/closure',
    'closure' => function() {
        return 'this is a closure';
    },
];

// Add module to all routes
foreach ($routes as &$route) {
    $route['module'] = $app->getModuleFromPath(__DIR__);
}
$app->getRouter()->addRoutes($routes);