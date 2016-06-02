<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Debug {

    /** @var int */
    protected $timerStart = 0;

    /** @var int */
    protected $timerEnd   = 0;

    /**
     * Pretty var_dump $data
     *
     * @param mixed $data
     */
    public function d($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    /**
     * Pretty print_r $data
     *
     * @param mixed $data
     */
    public function p($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * Pretty var_dump $data, then die
     *
     * @param mixed $data
     */
    public function dd($data) {
        $this->d($data);
        die();
    }

    /**
     * Pretty print_r $data, then die
     *
     * @param mixed $data
     */
    public function pd($data) {
        $this->p($data);
        die();
    }

    /**
     * Start the execution timer and returns the timerStart value
     *
     * @return int
     */
    public function startTimer() {
        $this->timerEnd = 0;
        $this->timerStart = microtime(true);
        return $this->timerStart;
    }

    /**
     * Ends the execution timer and returns the timer difference
     *
     * @return string
     */
    public function endTimer() {
        $this->timerEnd = microtime(true);
        return $this->getTimerDiff();
    }

    /**
     * Returns the difference between the end and the start in seconds
     *
     * @return string
     */
    public function getTimerDiff() {
        return number_format($this->timerEnd - $this->timerStart, 4);
    }

}
