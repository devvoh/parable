<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

trait AppTrait {

    /**
     * @var \Devvoh\Parable\App
     */
    protected $app;

    /**
     * Makes App available to classes using this trait
     */
    public function initApp() {
        $this->app = \Devvoh\Parable\App::getInstance();
    }

}