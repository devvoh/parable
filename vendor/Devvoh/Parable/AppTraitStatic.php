<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

trait AppTraitStatic {

    /**
     * @var \Devvoh\Parable\App
     */
    protected static $app;

    /**
     * Makes App available to classes using this trait
     */
    public static function initApp() {
        self::$app = \Devvoh\Parable\App::getInstance();
    }

}