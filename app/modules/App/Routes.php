<?php
/**
 * @package     Fluid
 * @subpackage  example Routes file
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

use \Devvoh\Fluid\App as App;

$routes['index'] = [
    'method' => 'GET',
    'path' => '/',
    'controller' => 'Home',
    'action' => 'index',
];
$routes['test'] = [
    'method' => 'GET',
    'path' => '/test',
    'controller' => 'Home\Test',
    'action' => 'index',
];

// Add module to all routes
foreach ($routes as &$route) {
    $route['module'] = App::getModuleFromPath(__DIR__);
}
App::getRouter()->addRoutes($routes);