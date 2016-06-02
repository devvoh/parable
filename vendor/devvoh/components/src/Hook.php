<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Hook {

    /** @var array */
    protected $hooks = [];

    /**
     * Add hook referencing $closure to $event, returns false if $closure isn't a function
     *
     * @param string   $event
     * @param callable $closure
     *
     * @return $this
     */
    public function into($event, callable $closure) {
        $this->hooks[$event][] = $closure;
        return $this;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $closures
     *
     * @param string     $event
     * @param null|mixed $payload
     *
     * @return $this
     */
    public function trigger($event, &$payload = null) {
        // Get all global hooks
        $globalHooks = [];
        if (isset($this->hooks['*']) && count($this->hooks['*']) > 0) {
            $globalHooks = $this->hooks['*'];
        }

        // Check if the event exists and has closures to call
        if (!isset($this->hooks[$event]) || count($this->hooks[$event]) == 0) {
            // There are no specific hooks, but maybe there's global hooks?
            if (count($globalHooks) === 0) {
                // There is nothing to do here
                return $this;
            }
            $hooks = $globalHooks;
        } else {
            $hooks = $this->hooks[$event];
            $hooks = array_merge($hooks, $globalHooks);
        }

        // All good, let's call those closures
        foreach ($hooks as $closure) {
            $closure($event, $payload);
        }

        // And return ourselves
        return $this;
    }
}
