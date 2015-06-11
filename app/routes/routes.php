<?php
/**
 * Fluid - routes.php
 *
 * Maps all known routes to the router
 *
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

$router = $app->getRouter();

$router->map(['GET'], '/', function($params) {
    echo 'home<br /><a href="./test">test</a>';
});

$router->map(['GET', 'POST'], '/test/{i:id}', function($params) {
    echo 'test<br /><a href="./">home</a>';
});

$router->map(['GET', 'POST'], '/hello/{a:name}', function($params) {
    echo 'Hello ' . $params['name'];
});