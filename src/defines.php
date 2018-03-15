<?php
// Here we define some constants Parable has a use of
if (!defined("DS")) {
    define("DS", DIRECTORY_SEPARATOR);
}
if (!defined("BASEDIR")) {
    define("BASEDIR", realpath(__DIR__ . DS . ".." . DS . ".." . DS . ".." . DS . ".."));
}
if (!defined("APP_CONTEXT")) {
    if (PHP_SAPI === "cli") {
        define("APP_CONTEXT", "console");
    } else {
        define("APP_CONTEXT", "web");
    }
}