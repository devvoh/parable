<?php

namespace Parable\Event;

class Dock
{
    /** @var array */
    protected $docks = [];

    /**
     * Dock into a frontend dock event trigger, adding a $callable.
     *
     * @param string      $event
     * @param callable    $callable
     * @param null|string $template
     *
     * @return $this|false
     */
    public function into($event, callable $callable, $template = null)
    {
        $this->docks[$event][] = [
            'callable' => $callable,
            'template' => $template,
        ];
        return $this;
    }

    /**
     * Trigger $event and run through all hooks referenced, passing along $payload to all $callables.
     *
     * @param null|string $event
     * @param null|mixed  $payload
     *
     * @return $this|bool
     */
    public function trigger($event = null, &$payload = null)
    {
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
            $dock['callable']($event, $payload);

            // And include the template if we have one. Data should be passed to the template through
            // outside means like through the session or \Parable\GetSet or one of its sub-types.
            if ($dock['template'] && file_exists($dock['template'])) {
                ob_start();
                require($dock['template']);
                $return = ob_get_clean();
                echo $return;
            }
        }
        return $this;
    }
}
