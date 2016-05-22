<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

/**
 * @method \Devvoh\Components\Validate      getValidate() get Devvoh\Components\Validate
 * @method \Devvoh\Components\Debug         getDebug() get Devvoh\Components\Debug
 * @method \Devvoh\Components\Database      getDatabase() get Devvoh\Components\Database
 * @method \Devvoh\Components\Date          getDate() get Devvoh\Components\Date
 * @method \Devvoh\Components\Exception     getException() get Devvoh\Components\Exception
 * @method \Devvoh\Components\Request       getRequest() get Devvoh\Components\Request
 * @method \Devvoh\Components\Curl          getCurl() get Devvoh\Components\Curl
 * @method \Devvoh\Components\Hook          getHook() get Devvoh\Components\Hook
 * @method \Devvoh\Components\Cli           getCli() get Devvoh\Components\Cli
 * @method \Devvoh\Components\Dock          getDock() get Devvoh\Components\Dock
 * @method \Devvoh\Components\Mailer        getMailer() get Devvoh\Components\Mailer
 * @method \Devvoh\Components\Router        getRouter() get Devvoh\Components\Router
 * @method \Devvoh\Components\Rights        getRights() get Devvoh\Components\Rights
 * @method \Devvoh\Components\Response      getResponse() get Devvoh\Components\Response
 * @method \Devvoh\Components\Log           getLog() get Devvoh\Components\Log
 * @method \Devvoh\Parable\Post             getPost() get Devvoh\Parable\Post
 * @method \Devvoh\Parable\Config           getConfig() get Devvoh\Parable\Config
 * @method \Devvoh\Parable\Session          getSession() get Devvoh\Parable\Session
 * @method \Devvoh\Parable\SessionMessage   getSessionMessage() get Devvoh\Parable\SessionMessage
 * @method \Devvoh\Parable\Auth             getAuth() get Devvoh\Parable\Auth
 * @method \Devvoh\Parable\Param            getParam() get Devvoh\Parable\Param
 * @method \Devvoh\Parable\Get              getGet() get Devvoh\Parable\Get
 * @method \Devvoh\Parable\Tool             getTool() get Devvoh\Parable\Tool
 * @method \Devvoh\Components\Validate      createValidate() create Devvoh\Components\Validate
 * @method \Devvoh\Components\Debug         createDebug() create Devvoh\Components\Debug
 * @method \Devvoh\Components\Database      createDatabase() create Devvoh\Components\Database
 * @method \Devvoh\Components\Date          createDate() create Devvoh\Components\Date
 * @method \Devvoh\Components\Exception     createException() create Devvoh\Components\Exception
 * @method \Devvoh\Components\Request       createRequest() create Devvoh\Components\Request
 * @method \Devvoh\Components\Curl          createCurl() create Devvoh\Components\Curl
 * @method \Devvoh\Components\Hook          createHook() create Devvoh\Components\Hook
 * @method \Devvoh\Components\Cli           createCli() create Devvoh\Components\Cli
 * @method \Devvoh\Components\Dock          createDock() create Devvoh\Components\Dock
 * @method \Devvoh\Components\Mailer        createMailer() create Devvoh\Components\Mailer
 * @method \Devvoh\Components\Router        createRouter() create Devvoh\Components\Router
 * @method \Devvoh\Components\Rights        createRights() create Devvoh\Components\Rights
 * @method \Devvoh\Components\Response      createResponse() create Devvoh\Components\Response
 * @method \Devvoh\Components\Log           createLog() create Devvoh\Components\Log
 * @method \Devvoh\Parable\Post             createPost() create Devvoh\Parable\Post
 * @method \Devvoh\Parable\Config           createConfig() create Devvoh\Parable\Config
 * @method \Devvoh\Parable\Session          createSession() create Devvoh\Parable\Session
 * @method \Devvoh\Parable\SessionMessage   createSessionMessage() create Devvoh\Parable\SessionMessage
 * @method \Devvoh\Parable\Auth             createAuth() create Devvoh\Parable\Auth
 * @method \Devvoh\Parable\Param            createParam() create Devvoh\Parable\Param
 * @method \Devvoh\Parable\Get              createGet() create Devvoh\Parable\Get
 * @method \Devvoh\Parable\Tool             createTool() create Devvoh\Parable\Tool
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