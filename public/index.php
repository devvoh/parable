<?php
/**
 * Fluid - index.php
 *
 * Main flow
 *
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

require(__DIR__ . '/../vendor/autoload.php');

$app = new \Devvoh\Fluid\App();

require(__DIR__ . '/../app/routes/routes.php');

$app->run();