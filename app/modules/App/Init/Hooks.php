<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Init;

class Hooks {
    use \Devvoh\Parable\AppTrait;

    public function __construct() {
        $this->initApp();

        // Register global loop to log all triggers
        $this->app->getHook()->into('*', function($event) {
            $this->app->getLog()->write('Hook triggered: ' . $event);
        });

        // Register the before hook
        $this->app->getHook()->into('parable_dispatcher_execute_before', function($event, &$payload) {
            // Do the stuff you want to do before dispatcher_execute
        });

        // Register the after hook
        $this->app->getHook()->into('parable_dispatcher_execute_after', function($event, &$payload) {
            // Do the stuff you want to do after dispatcher_execute
        });
    }

}