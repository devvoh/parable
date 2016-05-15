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
    protected static $timerStart = 0;

    /** @var int */
    protected static $timerEnd   = 0;

    /**
     * Pretty var_dump $data
     *
     * @param mixed $data
     */
    public static function d($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    /**
     * Pretty print_r $data
     *
     * @param mixed $data
     */
    public static function p($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * Pretty var_dump $data, then die
     *
     * @param mixed $data
     */
    public static function dd($data) {
        self::d($data);
        die();
    }

    /**
     * Pretty print_r $data, then die
     *
     * @param mixed $data
     */
    public static function pd($data) {
        self::p($data);
        die();
    }

    /**
     * Start the execution timer and returns the timerStart value
     *
     * @return int
     */
    public static function startTimer() {
        self::$timerEnd = 0;
        self::$timerStart = microtime(true);
        return self::$timerStart;
    }

    /**
     * Ends the execution timer and returns the timer difference
     *
     * @return string
     */
    public static function endTimer() {
        self::$timerEnd = microtime(true);
        return self::getTimerDiff();
    }

    /**
     * Returns the difference between the end and the start in seconds
     *
     * @return string
     */
    public static function getTimerDiff() {
        return number_format(self::$timerEnd - self::$timerStart, 4);
    }

}