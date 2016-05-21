<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class View {

    /** @var \Devvoh\Parable\Tool */
    protected $tool;

    /** @var \Devvoh\Components\Response */
    protected $response;

    /**
     * @param \Devvoh\Parable\Tool        $tool
     * @param \Devvoh\Components\Response $response
     */
    public function __construct(
        \Devvoh\Parable\Tool        $tool,
        \Devvoh\Components\Response $response
    ) {
        $this->tool     = $tool;
        $this->response = $response;
    }

    /**
     * Loads and shows the template file
     *
     * @param string $file
     * @return $this
     */
    public function loadTemplate($file) {
        if (!file_exists($file)) {
            return null;
        }
        $content = $this->render($file);
        $this->response->appendContent($content);
        return $this;
    }

    /**
     * Loads a partial into an output buffer and returns the parsed result
     *
     * @param string $file
     * @param null|string $module
     * @return null|string
     */
    public function partial($file, $module = null) {
        // Set the return value to null by default
        $return = null;

        // If a module is given, build a fitting array for consistency with App::getModules()
        $modules = [['name' => $module]];
        if (!$module) {
            $modules = $this->tool->getModules();
        }

        // Now loop through whatever array we're left with and bail on the first match. If a specific module is
        // wanted, pass a module to partial()
        foreach ($modules as $module) {
            $dir = 'app' . DS . 'modules' . DS . $module['name'] . DS . 'View' . DS . $file;
            $dir = $this->tool->getDir($dir);
            if (file_exists($dir)) {
                $return = $this->render($dir);
                break;
            }
        }
        return $return;
    }

    /**
     * Render the $file and return the interpreted code
     *
     * @param string$file
     * @return string
     */
    public function render($file) {
        $this->response->startOB();
        require($file);
        return $this->response->endOB();
    }

    public function __call($method, array $parameters = []) {
        if (method_exists('\Devvoh\Parable\App', $method)) {
            return call_user_func_array(['\Devvoh\Parable\App', $method], $parameters);
        }
        return false;
    }
}