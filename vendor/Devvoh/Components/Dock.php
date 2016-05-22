<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Dock {

    /** @var array */
    protected $docks = [];

    /**
     * Dock into a frontend dock event trigger
     *
     * @param null|string   $event
     * @param null|callable $closure
     * @param null|string   $viewFile
     * @return $this|false
     */
    public function into($event = null, $closure = null, $viewFile = null) {
        // Check if all data is given and correct
        if (!$event || !$closure || !is_callable($closure)) {
            return false;
        }

        // All good, add the event & closure to hooks
        $this->docks[$event][] = [
            'closure' => $closure,
            'viewFile' => $viewFile,
        ];

        return $this;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $closures
     *
     * @param null|string $event
     * @param null|mixed $payload
     * @return $this|false
     */
    public function trigger($event = null, &$payload = null) {
        // Check if all data is given and correct
        if (!$event || $event === '*') {
            return false;
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
                return false;
            }
            $docks = $globalDocks;
        } else {
            $docks = $this->docks[$event];
            $docks = array_merge($docks, $globalDocks);
        }

        // All good, let's call those closures
        foreach ($docks as $dock) {
            if (is_callable($dock['closure'])) {
                $dock['closure']($payload);
                // And include the viewFile if we have one. Data should be passed to the viewFile through
                // outside means, through the session, one of the global variables ($_GET, etc.) or through
                // Devvoh\Components\GetSet, if it's available.
                if ($dock['viewFile'] && file_exists($dock['viewFile'])) {
                    ob_start();
                    require($dock['viewFile']);
                    $return = ob_get_clean();
                    echo $return;
                }
            } else {
                return false;
            }
        }

        // And return ourselves
        return $this;
    }
}