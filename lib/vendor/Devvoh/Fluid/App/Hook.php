<?php
/**
 * @package     Fluid
 * @subpackage  Hook
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

class Hook {

    /**
     * @var array
     */
    static $hooks = array();

    /**
     * Add hook referencing $closure to $event, returns false if $closure isn't a function
     *
     * @param null|string   $event
     * @param null|callable $closure
     *
     * @return bool
     */
    public static function add($event = null, $closure = null) {
        // Check if all data is given and correct
        if (!$event || !$closure || !is_callable($closure)) {
            return false;
        }

        // All good, add the event & closure to hooks
        self::$hooks[$event][] = $closure;
        return true;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $closures
     *
     * @param null $event
     * @param null $payload
     *
     * @return bool
     */
    public static function trigger($event = null, &$payload = null) {
        // Check if all data is given and correct
        if (!$event) {
            return false;
        }

        // Check if the event exists and has closures to call
        if (!isset(self::$hooks[$event]) || count(self::$hooks[$event]) == 0) {
            return false;
        }

        // All good, let's call those closures
        foreach (self::$hooks[$event] as $closure) {
            $closure($payload);
        }
    }
}