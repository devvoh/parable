<?php
/**
 * @package     Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

ini_set('display_errors', '1');

$app = require_once(__DIR__ . '../vendor/devvoh/parable/src/Framework/Bootstrap.php');

$app->run();
