<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  App
 * @subpackage  Config
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid\App;

use \Devvoh\Fluid\App;

class Config extends \Devvoh\Components\GetSet {
    use \Devvoh\Components\Traits\GetClassName;

    protected $config = array();

    /**
     * Set the resource to config
     *
     * @return Config
     */
    public function __construct() {
        $this->setResource('config');
        return $this;
    }

    /**
     * Shim to allow App to proceed without config code existing
     *
     * @return Config
     */
    public function load() {
        // LOAD LOGIC HERE
        return $this;
    }

}