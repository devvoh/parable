<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Init;

use \Devvoh\Parable\App;

class Hooks {

    public $order = 1;

    public function run() {
        // Register global loop to log all triggers
        App::Hook()->into('*', function($event) {
            App::Log()->write('Hook triggered: ' . $event);
        });

        // Register the before hook
        App::Hook()->into('parable_dispatcher_execute_before', function($event, &$payload) {
            // Do the stuff you want to do before dispatcher_execute
        });

        // Register the after hook
        App::Hook()->into('parable_dispatcher_execute_after', function($event, &$payload) {
            // Do the stuff you want to do after dispatcher_execute
        });
    }

}