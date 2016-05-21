<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Init;

class Hooks {

    public $order = 1;

    /** @var \Devvoh\Components\Hook */
    protected $hook;

    /** @var \Devvoh\Components\Log  */
    protected $log;

    /**
     * @param \Devvoh\Components\Hook $hook
     * @param \Devvoh\Components\Log  $log
     */
    public function __construct(
        \Devvoh\Components\Hook $hook,
        \Devvoh\Components\Log  $log
    ) {
        $this->hook = $hook;
        $this->log  = $log;
    }

    public function run() {
        // Register global loop to log all triggers
        $this->hook->into('*', function($event) {
            $this->log->write('Hook triggered: ' . $event);
        });

        // Register the before hook
        $this->hook->into('parable_dispatcher_execute_before', function($event, &$payload) {
            // Do the stuff you want to do before dispatcher_execute
        });

        // Register the after hook
        $this->hook->into('parable_dispatcher_execute_after', function($event, &$payload) {
            // Do the stuff you want to do after dispatcher_execute
        });
    }

}