<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Dock
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Dock {

    protected $docks = [];

    /**
     * Dock into a frontend dock event trigger
     *
     * @param null|string   $event
     * @param null|callable $closure
     * @param null          $viewFile
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

        // And return ourselves
        return $this;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $closures
     *
     * @param null $event
     * @param null $payload
     * @return $this|false
     */
    public function trigger($event = null, &$payload = null) {
        // Check if all data is given and correct
        if (!$event) {
            return false;
        }

        // Check if the event exists and has closures to call
        if (!isset($this->docks[$event]) || count($this->docks[$event]) == 0) {
            return false;
        }

        // All good, let's call those closures
        foreach ($this->docks[$event] as $dock) {
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