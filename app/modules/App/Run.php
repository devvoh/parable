<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App;

use \Devvoh\Parable\App as App;

class Run {

    public function preDispatch() {
        // Do whatever preDispatch stuff you want to do here. This is BEFORE the controller/closure is called.
    }

    public function postDispatch() {
        // Do whatever postDispatch stuff you want to do here. This is AFTER the view template is included.
    }

}