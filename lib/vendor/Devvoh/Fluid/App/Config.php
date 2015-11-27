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

class Config {
    use \Devvoh\Components\Traits\GetClassName;

    /**
     * Shim to allow App to proceed without config code existing
     *
     * @return $this
     */
    public function load() {
        return $this;
    }

}