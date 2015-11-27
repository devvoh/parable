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
    // If $class is literally App, we get it directly. We do this so view files can just use App instead of the full
    // namespaced name.
    if ($class === 'App') {
        $path = '../lib/vendor/Devvoh/Fluid/App.php';
        require_once($path);
        return true;
    }

    // Otherwise, do it the proper way
    $path = str_replace('\\', DS, $class);
    $path = '../lib/vendor/' . trim($path, DS) . '.php';
    $path = str_replace('_', DS, $path);
    $path = str_replace('/', DS, $path);

    if (file_exists($path)) {
        require_once($path);
    } else {
        throw new Exception('Unable to autoload ' . $class . ' (' . $path . ')');
    }
});

/**
 * Start output buffering with gzip compression if possible
 */
if(!ob_start("ob_gzhandler")) {
    // No ob_gzhandler so regular
    ob_start();
}