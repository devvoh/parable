<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

/**
 * @property \Devvoh\Components\Cli           $cli
 * @property \Devvoh\Components\Curl          $curl
 * @property \Devvoh\Components\Database      $database
 * @property \Devvoh\Components\Date          $date
 * @property \Devvoh\Components\Debug         $debug
 * @property \Devvoh\Components\Dock          $dock
 * @property \Devvoh\Components\Exception     $exception
 * @property \Devvoh\Components\Hook          $hook
 * @property \Devvoh\Components\Log           $log
 * @property \Devvoh\Components\Mailer        $mailer
 * @property \Devvoh\Components\Response      $response
 * @property \Devvoh\Components\Request       $request
 * @property \Devvoh\Components\Rights        $rights
 * @property \Devvoh\Components\Router        $router
 * @property \Devvoh\Components\Validate      $validate
 * @property \Devvoh\Parable\Auth             $auth
 * @property \Devvoh\Parable\Config           $config
 * @property \Devvoh\Parable\Get              $get
 * @property \Devvoh\Parable\Param            $param
 * @property \Devvoh\Parable\Post             $post
 * @property \Devvoh\Parable\Session          $session
 * @property \Devvoh\Parable\SessionMessage   $sessionMessage
 * @property \Devvoh\Parable\Tool             $tool
 */
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

    public function __call($resourceGetter, $params) {
        if (substr($resourceGetter, 0, 3) == 'get') {
            $resource = substr($resourceGetter, 3);
            $mapping = $this->tool->getResourceMapping($resource);
            if ($mapping) {
                return \Devvoh\Components\DI::get($mapping);
            }
        }
        if (substr($resourceGetter, 0, 6) == 'create') {
            $resource = substr($resourceGetter, 6);
            $mapping = $this->tool->getResourceMapping($resource);
            if ($mapping) {
                return \Devvoh\Components\DI::create($mapping);
            }
        }
    }

}