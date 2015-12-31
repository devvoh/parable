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
    // Otherwise, do it the proper way by turning the class into a file path
    $path = str_replace('\\', DS, $class);
    $path = '../vendor/' . trim($path, DS) . '.php';
    $path = str_replace('_', DS, $path);
    $path = str_replace('/', DS, $path);

    if (file_exists($path)) {
        require_once($path);
        return true;
    }
    return false;
});
/**
 * Register Fluid entity autoloader
 */
spl_autoload_register(function ($class) {
    $modelTypes = \Devvoh\Fluid\App::getModelTypes();
    $modelName = null;
    foreach ($modelTypes as $modelType) {
        if (strpos($class, '_'.$modelType) !== false) {
            // Remove modelType from the end in the most concrete way possible (str_replace might remove too much,
            // and rtrim sometimes removes too much as well).
            $classParts = explode('_', $class);
            $lastPart = count($classParts) - 1;
            $lastPartName = $classParts[$lastPart];
            if ($lastPartName == $modelType) {
                unset($classParts[$lastPart]);
            }
            $modelName = implode('_', $classParts);
        }
    }

    if ($modelName) {
        foreach (\Devvoh\Fluid\App::getModules() as $module) {
            $path = $module['path'] . DS . $lastPartName . DS . $modelName . '.php';
            if (is_file($path)) {
                require_once($path);
                return true;
            }
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