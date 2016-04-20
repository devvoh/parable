<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

/**
 * Define some global values
 */
define('DS', DIRECTORY_SEPARATOR);
define('BASEDIR', __DIR__ . DS . '..' . DS . '..' . DS . '..');

/**
 * Set error reporting level
 */
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

/**
 * Register PSR-4 compatible autoloader
 */
$autoloadPath = BASEDIR . DS . 'vendor' . DS . 'Devvoh' . DS . 'Components' . DS . 'Autoloader.php';
require_once($autoloadPath);

$autoloader = new \Devvoh\Components\Autoloader();
$autoloader->addLocation(BASEDIR . DS . 'vendor');
$autoloader->addLocation(BASEDIR . DS . 'app' . DS . 'modules');
$autoloader->register();

/**
 * And run boot on App to get it all started
 */
$app = \Devvoh\Parable\App::getInstance();
$app->boot();

return $app;