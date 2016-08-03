<?php
/**
 * @package     Parable Events
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Events;

class Dock {

    /** @var array */
    protected $docks = [];

    /**
     * Dock into a frontend dock event trigger, adding a $callable
     *
     * @param string      $event
     * @param callable    $callable
     * @param null|string $viewFile
     * @return $this|false
     */
    public function into($event, callable $callable, $viewFile = null) {
        $this->docks[$event][] = [
            'callable' => $callable,
            'viewFile' => $viewFile,
        ];
        return $this;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $callables
     *
     * @param null $event
     * @param null $payload
     *
     * @return $this|bool
     */
    public function trigger($event = null, &$payload = null) {
        // Disallow calling a trigger on global docks
        if ($event === '*') {
            return $this;
        }

        // Get all global docks
        $globalDocks = [];
        if (isset($this->docks['*']) && count($this->docks['*']) > 0) {
            $globalDocks = $this->docks['*'];
        }

        // Check if the event exists and has closures to call
        if (!isset($this->docks[$event]) || count($this->docks[$event]) == 0) {
            // There are no specific hooks, but maybe there's global hooks?
            if (count($globalDocks) === 0) {
                // There is nothing to do here
                return $this;
            }
            $docks = $globalDocks;
        } else {
            $docks = $this->docks[$event];
            $docks = array_merge($docks, $globalDocks);
        }

        // All good, let's call those closures
        foreach ($docks as $dock) {
            $dock['callable']($payload);
            // And include the viewFile if we have one. Data should be passed to the viewFile through
            // outside means, through the session, one of the global variables ($_GET, etc.) or through
            // Devvoh\Components\GetSet, if it's available.
            if ($dock['viewFile'] && file_exists($dock['viewFile'])) {
                ob_start();
                require($dock['viewFile']);
                $return = ob_get_clean();
                echo $return;
            }
        }
        return $this;
    }
}