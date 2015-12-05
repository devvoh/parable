<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Debug
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Debug {
    use \Devvoh\Components\Traits\GetClassName;

    protected static $timerStart = 0;
    protected static $timerEnd = 0;

    /**
     * Pretty var_dump $data
     *
     * @param $data
     */
    public static function d($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    /**
     * Pretty var_dump $data, then die
     *
     * @param $data
     */
    public static function dd($data) {
        self::d($data);
        die();
    }

    /**
     * Pretty print_r $data
     *
     * @param $data
     */
    public static function p($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * Pretty print_r $data, then die
     *
     * @param $data
     */
    public static function pd($data) {
        self::p($data);
        die();
    }

    public static function startTimer() {
        self::$timerStart = microtime(true);
    }

    public static function endTimer() {
        self::$timerEnd = microtime(true);
    }

    public static function getTimerDiff() {
        return number_format(self::$timerEnd - self::$timerStart, 4);
    }

}