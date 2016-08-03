<?php
/**
 * @package     Parable Framework
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

/*
 * Define some global values
 */
define('DS', DIRECTORY_SEPARATOR);
define('BASEDIR', realpath(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . '..' . DS  . '..'));

/*
 * Set error reporting level
 */
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

/*
 * Attempt to register composer's autoloader, which will be required for components
 */
if (!file_exists(BASEDIR . '/vendor/autoload.php')) {
    throw new \Exception('composer autoload not found, run "composer install" first to generate it.');
}
require_once(BASEDIR . '/vendor/autoload.php');

/*
 * And load and register the framework's autoloader
 */
$autoloader = \Parable\DI\Container::get(\Parable\Framework\Autoloader::class);
$autoloader->addLocation(BASEDIR . '/app');
$autoloader->register();

if (PHP_SAPI === 'cli') {
    return \Parable\DI\Container::get(\Parable\Able\App::class);
}
return \Parable\DI\Container::get(\Parable\Framework\App::class);