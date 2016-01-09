<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  Bootstrap
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
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
ini_set('display_errors', '1');

/**
 * Register Fluid App autoloader
 */
spl_autoload_register(function ($class) {
    if ($class === 'App') {
        $path = '../vendor/Devvoh/Fluid/App.php';
        require_once($path);
        return true;
    }
    return false;
});
/**
 * Register PSR-4 autoloader
 */
spl_autoload_register(function ($class) {
    $subPaths = ['vendor', 'app/modules'];

    // Otherwise, do it the proper way by turning the class into a file path
    $path = str_replace('\\', DS, $class);
    $path = '../##replace##/' . trim($path, DS) . '.php';
    $path = str_replace('/', DS, $path);

    foreach ($subPaths as $subPath) {
        $actualPath = str_replace('##replace##', $subPath, $path);
        if (file_exists($actualPath)) {
            require_once($actualPath);
            return true;
        }
    }
    return false;
});

/**
 * Start output buffering with gzip compression if possible
 */
if(!ob_start("ob_gzhandler")) {
    // No ob_gzhandler so regular ob_start
    ob_start();
}