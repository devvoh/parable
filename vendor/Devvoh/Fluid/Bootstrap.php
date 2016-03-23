<?php
/**
 * @package     Devvoh Fluid
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
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
 * Register PSR-4 compatible autoloader
 */
$autoloadPath = __DIR__ . DS . '..' . DS . 'Components' . DS . 'Autoloader.php';
require_once($autoloadPath);

$autoloader = new \Devvoh\Components\Autoloader();
$autoloader->addLocation('vendor');
$autoloader->addLocation('app' . DS . 'modules');
$autoloader->register();