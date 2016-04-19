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
        echo 'preDispatch RUN';
    }

    public function postDispatch() {
        echo 'postDispatch RUN';
    }

}