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

}