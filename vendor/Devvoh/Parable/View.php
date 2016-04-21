<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

use \Devvoh\Parable\App;

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
        $content = $this->render($file);
        App::getResponse()->appendContent($content);
        return $this;
    }

    /**
     * Loads a partial into an output buffer and returns the parsed result
     *
     * @param             $file
     * @param string|null $module
     *
     * @return null|string
     */
    public function partial($file, $module = null) {
        // Look for a module, either as given or a current module. If neither, assume Core.
        if (!$module) {
            $module = 'Core';
            if (App::getRoute()) {
                $module =App::getRoute()['module'];
            }
        }
        // Build proper path
        $dir = 'app' . DS . 'modules' . DS . $module . DS . 'View' . DS . $file;
        $dir = App::getDir($dir);

        // Set return value to null as default
        $return = null;
        if (file_exists($dir)) {
            $return = $this->render($dir);
        }
        return $return;
    }

    public function render($file) {
        App::getResponse()->startOB();
        require($file);
        return App::getResponse()->endOB();
    }

    /**
     * Allow view files to try to call static methods on App, to prevent use in phtml files or awkward
     * \Devvoh\Parable\App calls
     *
     * Possible uses:
     *    self::getGet()->getValues();
     *    $this->getGet()->getValues();
     *
     * Instead of:
     *    \Devvoh\Parable\App::getInstance()->getGet()->getValues();
     *    use \Devvoh\Parable\App; App::getInstance()->getGet()->getValues();
     *
     * @param $method
     * @param $parameters
     *
     * @return bool
     */
    public function __call($method, $parameters = []) {
        if (method_exists('\Devvoh\Parable\App', $method)) {
            return call_user_func_array(['\Devvoh\Parable\App', $method], $parameters);
        }
        return false;
    }
}