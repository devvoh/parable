<?php
/**
 * @package     Fluid
 * @subpackage  App
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid;

class App {

    static protected $baseDir   = null;
    static protected $publicUrl = null;
    static protected $debug     = null;
    static protected $cli       = null;
    static protected $config    = null;
    static protected $hook      = null;
    static protected $log       = null;
    static protected $param     = null;
    static protected $post      = null;
    static protected $get       = null;
    static protected $messages  = null;
    static protected $session   = null;
    static protected $response  = null;
    static protected $router    = null;
    static protected $database  = null;

    /**
     * Starts the App class and does some initial setup
     */
    public static function start() {
        // If debug = 1, enableDebug, otherwise, disable
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            self::enableDebug();
        } else {
            self::disableDebug();
        }

        // And load the App config
        self::getConfig()->load();

        // Start the session
        self::getSession()->startSession();
    }

    /**
     * Returns the base directory for the application.
     *
     * @return null|string
     */
    public static function getBaseDir() {
        if (!self::$baseDir) {
            $baseDir = rtrim(getcwd(), DS);
            self::$baseDir = rtrim($baseDir, 'public');
        }
        return self::$baseDir;
    }

    /**
     * Returns the public url for the application.
     *
     * @return null|string
     */
    public static function getPublicUrl() {
        if (!self::$publicUrl) {
            // Now get the complete public url & store it
            if (!isset($_SERVER['REQUEST_SCHEME'])) {
                if (!isset($_SERVER['REDIRECT_REQUEST_SCHEME'])) {
                    // Assume http since we don't know
                    $_SERVER['REQUEST_SCHEME'] = 'http';
                } else {
                    $_SERVER['REQUEST_SCHEME'] = $_SERVER['REDIRECT_REQUEST_SCHEME'];
                }
            }
            $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

            $url = rtrim($url, '/');
            $url = rtrim($url, 'public');

            self::$publicUrl = rtrim($url, '/');
        }
        return self::$publicUrl;
    }

    /**
     * Returns a dir based on the base dir
     *
     * @param null|string $path
     *
     * @return null|string
     */
    public static function getDir($path = null) {
        $dir = self::getBaseDir();
        if ($path) {
            $dir = rtrim($dir, DS) . DS . trim($path, DS);
        }
        return $dir;
    }

    /**
     * Returns an url based on the public url
     *
     * @param null|string $path
     *
     * @return null|string
     */
    public static function getUrl($path = null) {
        $url = self::getPublicUrl();
        if ($path) {
            $url = $url . '/' . trim($path, '/');
        }
        return $url;
    }

    /**
     * Enables debug mode, which will display errors. Errors are always logged (see Bootstrap.php)
     */
    public static function enableDebug() {
        self::$debug = true;
        ini_set('display_errors', '1');
    }

    /**
     * Disables debug mode, which will hide errors. Errors are always logged (see Bootstrap.php)
     */
    public static function disableDebug() {
        self::$debug = false;
        ini_set('display_errors', '0');
    }

    /**
     * Returns (and possibly instantiates) the Cli instance
     *
     * @return Cli
     */
    public static function getCli() {
        if (!self::$cli) {
            self::$cli = new \Devvoh\Components\Cli();
        }
        return self::$cli;
    }

    /**
     * Returns (and possibly instantiates) the Config instance
     *
     * @return Config
     */
    public static function getConfig() {
        if (!self::$config) {
            self::$config = new \Devvoh\Fluid\App\Config();
        }
        return self::$config;
    }

    /**
     * Returns (and possibly instantiates) the Hook instance
     *
     * @return Hook
     */
    public static function getHook() {
        if (!self::$hook) {
            self::$hook = new \Devvoh\Components\Hooks();
        }
        return self::$hook;
    }

    /**
     * Returns (and possibly instantiates) the Log instance
     *
     * @return Messages
     */
    public static function getLog() {
        if (!self::$log) {
            self::$log = new \Devvoh\Fluid\App\Log();
        }
        return self::$log;
    }

    /**
     * Returns (and possibly instantiates) the Messages instance
     *
     * @return Messages
     */
    public static function getMessages() {
        if (!self::$messages) {
            self::$messages = new \Devvoh\Fluid\App\Messages();
        }
        return self::$messages;
    }

    /**
     * Returns (and possibly instantiates) the Session instance
     *
     * @return Session
     */
    public static function getSession() {
        if (!self::$session) {
            self::$session = (new \Devvoh\Components\GetSet())->setResource('session');
        }
        return self::$session;
    }

    /**
     * Returns (and possibly instantiates) the Param instance
     *
     * @return Param
     */
    public static function getParam() {
        if (!self::$param) {
            self::$param = (new \Devvoh\Components\GetSet())->setResource('param');;
        }
        return self::$param;
    }

    /**
     * Returns (and possibly instantiates) the Post instance
     *
     * @return Post
     */
    public static function getPost() {
        if (!self::$post) {
            self::$post = (new \Devvoh\Components\GetSet())->setResource('post');;
        }
        return self::$post;
    }

    /**
     * Returns (and possibly instantiates) the Get instance
     *
     * @return Get
     */
    public static function getGet() {
        if (!self::$get) {
            self::$get = (new \Devvoh\Components\GetSet())->setResource('get');;
        }
        return self::$get;
    }

    /**
     * Returns (and possibly instantiates) the Router instance
     *
     * @return Router
     */
    public static function getRouter() {
        if (!self::$router) {
            self::$router = new \Devvoh\Components\Router();
            self::collectRoutes();
        }
        return self::$router;
    }

    /**
     * Returns (and possibly instantiates) the Database instance
     *
     * @return Database
     */
    public static function getDatabase() {
        if (!self::$database) {
            self::$database = new \Devvoh\Fluid\App\Database();
        }
        return self::$database;
    }

    /**
     * Returns (and possibly instantiates) the Response instance
     *
     * @return Response
     */
    public static function getResponse() {
        if (!self::$response) {
            self::$response = new \Devvoh\Fluid\App\Response();
        }
        return self::$response;
    }

    /**
     * Collect routes and include the files, which will add their routes to the router automatically
     */
    public static function collectRoutes() {
        $dir = self::getDir('app/modules') . DS . '*';
        foreach (glob($dir) as $filename) {
            $routerFilename = $filename . DS . 'routes' . DS . 'routes.php';
            if (file_exists($routerFilename)) {
                require_once($routerFilename);
            }
        }
    }

    /**
     * Executes a route
     *
     * @param $route
     *
     * @return bool
     */
    public static function executeRoute($route) {
        // Check for params
        if (isset($route['params'])) {
            foreach ($route['params'] as $param) {
                if (isset($param['name']) && isset($param['value'])) {
                    self::getParam()->set($param['name'], $param['value']);
                }
            }
        }
        // Check for a closure
        if (isset($route['closure'])) {
            $closure = $route['closure'];
            // Check if we can call it
            if (is_callable($closure)) {
                // Call it
                $closure();
            } else {
                // Not callable, so false
                return false;
            }

            // Check for view param
            if (isset($route['view'])) {
                $viewFile = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'view' . DS . $route['view'] . '.phtml';
            }
        } else {
            // Not a closure, build a controller/action combination
            $controllerFile = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'controller' . DS . $route['controller'] . '.php';
            $viewFile = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'view' . DS . $route['controller'] . DS . $route['action'] . '.phtml';
            if (file_exists($controllerFile)) {
                require_once($controllerFile);
                // Get all the data
                $controllerName = $route['controller'];
                $action         = $route['action'];
                $controller     = new $controllerName();
                // And call the action if it exists
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        if (isset($viewFile) && file_exists($viewFile)) {
            $view = new \Devvoh\Fluid\View();
            $view->loadTemplate($viewFile);
        }
        return true;
    }

}
