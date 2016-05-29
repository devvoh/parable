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
define('BASEDIR', realpath(__DIR__ . DS . '..' . DS . '..' . DS . '..'));

/**
 * Set error reporting level
 */
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

/**
 * Set Exception handler
 */
set_exception_handler(function(\Exception $e) {
    ?>
<pre style="border:1px solid #d00;background:#eee;padding: 0.5rem;">
    <h3 style="margin: 0;">Uncaught <?=get_class($e);?></h3>
    in "<strong><?=$e->getFile();?>" on line <?=$e->getLine();?></strong><br />
    <?=$e->getMessage();?><br />
    <?=$e->getTraceAsString();?><br />
</pre>
    <?php
});

/**
 * Register PSR-4 compatible autoloader
 */
$autoloadPath = BASEDIR . DS . 'vendor' . DS . 'Devvoh' . DS . 'Components' . DS . 'Autoloader.php';
$diPath = BASEDIR . DS . 'vendor' . DS . 'Devvoh' . DS . 'Components' . DS . 'DI.php';
require_once($autoloadPath);
require_once($diPath);

$autoloader = \Devvoh\Components\DI::get(\Devvoh\Components\Autoloader::class);
$autoloader->addLocation(BASEDIR . DS . 'vendor');
$autoloader->addLocation(BASEDIR . DS . 'app' . DS . 'modules');
$autoloader->register();

/**
 * And run boot on App to get it all started
 *
 * @var \Devvoh\Parable\App $app
 */
$app = \Devvoh\Components\DI::get(\Devvoh\Parable\App::class);
$app->boot();
return $app;