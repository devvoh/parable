<?php
/**
 * @package     Fluid
 * @subpackage  App
 * @subpackage  Config
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

use \Devvoh\Fluid\App as App;

class Config extends \Devvoh\Components\GetSet {
    use \Devvoh\Components\Traits\GetClassName;

    protected $config = array();

    /**
     * Set the resource to config
     *
     * @return $this
     */
    public function __construct() {
        $this->setResource('config');
        return $this;
    }

    /**
     * Shim to allow App to proceed without config code existing
     *
     * @return $this
     */
    public function load() {
        // LOAD LOGIC HERE
        return $this;
    }

}