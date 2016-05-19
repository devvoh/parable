<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

$app    = \Devvoh\Components\DI::get('\Devvoh\Parable\App');
$tool   = \Devvoh\Components\DI::get('\Devvoh\Parable\Tool');
$router = \Devvoh\Components\DI::get('\Devvoh\Components\Router');

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
    $route['module'] = $tool->getModuleFromPath(__DIR__);
}
$router->addRoutes($routes);