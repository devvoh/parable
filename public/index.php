<?php
/**
 * @package     Fluid
 * @subpackage  router
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Include Bootstrap.php to enable all functionality.
 */
require_once('../lib/vendor/Devvoh/Fluid/Bootstrap.php');
use Devvoh\Fluid\App as App;

/**
 * App is the main entry point for all functionality, offering mostly static functions.
 *
 * Start the App. This will set debug and load the config
 */
App::start();

/**
 * Try to match the path to an existing route. If no path given to ->route(), current $_GET value is used.
 */
$route = App::getRouter()->route();

var_dump(App::getPost()->getNameSpace());

if ($route) {
    if (!App::getRouter()->execute($route)) {
        // thar be error?
    }
} else {
    // no route, 404
}
