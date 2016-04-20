#!/usr/bin/env php
<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Include Bootstrap.php to enable all functionality.
 */
require_once('./vendor/Devvoh/Parable/Bootstrap.php');

/**
 * Now we boot the Cli sub-app, passing the cli arguments
 */
\Devvoh\Parable\Cli::boot($argv);

/**
 * And we run!
 */
\Devvoh\Parable\Cli::run();
