<?php
/**
 * @package     Fluid
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Include Bootstrap.php to enable all functionality.
 */
require_once('./vendor/Devvoh/Fluid/Bootstrap.php');

/**
 * App is the main entry point for all functionality, offering mostly static functions.
 *
 * Run the App. This will set debug and load the config
 */
\Devvoh\Fluid\App::run();

/**
 * Now we boot the Cli sub-app, passing the cli arguments
 */
\Devvoh\Fluid\Cli::boot($argv);

/**
 * And we run!
 */
\Devvoh\Fluid\Cli::run();
