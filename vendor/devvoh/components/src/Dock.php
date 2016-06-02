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
     * @param string      $event
     * @param callable    $closure
     * @param null|string $viewFile
     *
     * @return $this
     */
    public function into($event, callable $closure, $viewFile = null) {
        $this->docks[$event][] = [
            'closure' => $closure,
            'viewFile' => $viewFile,
        ];
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
            $dock['closure']($payload);
            if ($dock['viewFile'] && file_exists($dock['viewFile'])) {
                ob_start();
                require($dock['viewFile']);
                $return = ob_get_clean();
                echo $return;
            }
        }

        // And return ourselves
        return $this;
    }
}
