<?php
/**
 * @package     Fluid
 * @subpackage  router
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Include Bootstrap.php to enable all functionality.
 */
require_once('../vendor/Devvoh/Fluid/Bootstrap.php');
use \Devvoh\Fluid\App;

/**
 * App is the main entry point for all functionality, offering mostly static functions.
 *
 * Start the App. This will set debug and load the config
 */
App::start();

/**
 * Try to match the path to an existing route. If no path given to ->route(), current $_GET value is used.
 */
if (App::matchRoute()) {
    if (!App::executeRoute()) {
        echo 'Route found but there\'s something wrong. Possibly the controller or action doesn\'t exist.';
    }
} else {
    echo App::getView()->partial('Error/404.phtml');
}

/**
 * Last thing we do is ask our Response to send it all as configured
 */
App::getResponse()->sendResponse();