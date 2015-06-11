<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require(__DIR__ . '/lib/vendor/autoload.php');

$app = new \Devvoh\Fluid\App();

$app->getRouter()->map(['GET'], '/', function($params) {
    echo 'home<br /><a href="./test">test</a> 345345345';
    var_dump($params);
});
$app->getRouter()->map(['GET', 'POST'], '/test/{i:id}', function($params) {
    echo 'test<br /><a href="./">home</a> 2345345345';
    var_dump($params);
});
$app->getRouter()->map(['GET', 'POST'], '/hello/{name}', function($params) {
    echo 'Hello ' . $params['name'];
});

$app->run();