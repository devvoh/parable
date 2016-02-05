<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  App
 * @subpackage  View
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid\App;

use \Devvoh\Fluid\App;

class View {

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
        require($file);
        return $this;
    }

    /**
     * Loads a partial into an output buffer and returns the parsed result
     *
     * @param        $file
     * @param string $module
     *
     * @return null|string
     */
    public function partial($file, $module = 'Base') {
        if (App::getRoute()) {
            $module = App::getRoute()['module'];
        }
        // Build proper path
        $dir = 'app' . DS . 'modules' . DS . $module . DS . 'View' . DS . $file;
        $dir = App::getDir($dir);

        // Set return value to null as default
        $return = null;
        if (file_exists($dir)) {
            ob_start();
            require($dir);
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
     *    use \Devvoh\Fluid\App; App::getGet()->getValues();
     *
     * @param $method
     * @param $args
     *
     * @return bool
     */
    public function __call($method, $args) {
        if (count($args) == 1) {
            $args = $args[0];
        }
        if (method_exists('\Devvoh\Fluid\App', $method)) {
            return App::$method($args);
        }
        return false;
    }
}