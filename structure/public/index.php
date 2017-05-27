<?php
ini_set('display_errors', '1');

$bootstrapDirectory = __DIR__ . '/../vendor/devvoh/parable/src/Framework/Bootstrap.php';
if (!file_exists($bootstrapDirectory)) {
    die("<b>ERROR</b>: You need to run <pre style='display:inline-block;background:#e6e6e6;padding:2px 5px'>composer install</pre> before Parable will work.");
}

$app = require_once($bootstrapDirectory);

$app->run();
