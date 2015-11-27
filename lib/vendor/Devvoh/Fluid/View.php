<?php
/**
 * @package     Fluid
 * @subpackage  View
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid;

use \Devvoh\Fluid\App as App;

class View {
    use \Devvoh\Components\Traits\GetClassName;

    /**
     * Loads and shows the template file
     *
     * @param $file
     *
     * @return $this
     */
    public function loadTemplate($file) {
        if (!file_exists($file)) {
            return null;
        }
        include_once($file);
        return $this;
    }

    public function partial($file) {
        // Build proper path
        $dir = 'app' . DS . 'modules' . DS . App::getRoute()['module'] . DS . 'view' . DS . $file;
        $path = App::getDir($dir);

        // Set return value to null as default
        $return = null;
        if (file_exists($path)) {
            ob_start();
            require($path);
            $return = ob_get_clean();
        }
        return $return;
    }

    /**
     * Allow view files to try to call static methods on App, to prevent use in phtmls or awkward
     * \Devvoh\Fluid\App calls
     *
     * Possible uses:
     *    self::getGet()->getValues();
     *    $this->getGet()->getValues();
     *
     * Instead of:
     *    \Devvoh\Fluid\App::getGet()->getValues();
     *    use \Devvoh\Fluid\App as App; & App::getGet()->getValues();
     *
     * @param $method
     * @param $args
     *
     * @return bool
     */
    public function __call($method, $args) {
        if (method_exists('\Devvoh\Fluid\App', $method)) {
            return App::$method($args);
        }
        return false;
    }
}