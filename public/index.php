<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Include Bootstrap.php to enable all functionality.
 *
 * This will set all base values and allow use of all App->methods
 */
$app = require_once('../vendor/Devvoh/Parable/Bootstrap.php');

/**
 * Dispatch the current route.
 */
$app->dispatch();