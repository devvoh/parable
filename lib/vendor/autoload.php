<?php
spl_autoload_register(function($class) {
    $lib = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (is_file($lib)) {
        require($lib);
    }
});