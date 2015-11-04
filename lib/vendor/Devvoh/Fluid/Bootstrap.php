<?php
/**
 * @package     Fluid
 * @subpackage  Bootstrap
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Define some global values
 */
define(DS, DIRECTORY_SEPARATOR);

/**
 * Set error reporting level
 */
error_reporting(E_ALL);
ini_set('log_errors', '1');

/**
 * Register autoloader
 */
spl_autoload_register(function ($class) {
    $path = str_replace('\\', DS, $class);

    $path = '../lib/vendor/' . trim($path, DS) . '.php';
    $path = str_replace('/', DS, $path);

    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new Exception('Unable to autoload ' . $class);
    }
});