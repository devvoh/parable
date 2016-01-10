<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  App
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid;

class App {

    static protected $version           = null;
    static protected $baseDir           = null;
    static protected $publicUrl         = null;
    static protected $debugEnabled      = null;
    static protected $cli               = null;
    static protected $config            = null;
    static protected $hook              = null;
    static protected $dock              = null;
    static protected $log               = null;
    static protected $param             = null;
    static protected $post              = null;
    static protected $get               = null;
    static protected $session           = null;
    static protected $sessionMessage    = null;
    static protected $response          = null;
    static protected $router            = null;
    static protected $database          = null;
    static protected $route             = null;
    static protected $debug             = null;
    static protected $view              = null;
    static protected $rights            = null;
    static protected $date              = null;
    static protected $curl              = null;
    static protected $validate          = null;
    static protected $currentModule     = null;
    static protected $modules           = [];

    /**
     * Starts the App class and does some initial setup
     */
    public static function start() {
        // Find out what modules we have
        self::loadModules();

        // And load the App config
        self::getConfig()->load();

        // Set debug enabled/disabled based on config
        if (self::getConfig()->get('debug_enabled')) {
            self::enableDebug();
            self::getDebug()->startTimer();
        } else {
            self::disableDebug();
        }

        // Set and enable database based on config
        if (self::getConfig()->get('storage_type') && self::getConfig()->get('storage_location')) {
            $config = [
                'type'     => self::getConfig()->get('storage_type'),
                'location' => self::getDir(self::getConfig()->get('storage_location')),
                'username' => self::getConfig()->get('storage_username'),
                'password' => self::getConfig()->get('storage_password'),
                'database' => self::getConfig()->get('storage_database'),
            ];
            self::getDatabase()->setConfig($config);
        }

        // Set timezone if given, otherwise default to Europe/London
        self::getDate()->setTimezone('Europe/London');
        if (self::getConfig()->get('default_timezone')) {
            self::getDate()->setTimezone(self::getConfig()->get('default_timezone'));
        }

        // Set the appropriate log directory & default file name
        self::getLog()->setPath(self::getBaseDir() . 'var' . DS . 'log');

        // And see if there's additional rights levels we should add
        if (self::getConfig()->get('rights_add')) {
            $toAdd = explode(',', self::getConfig()->get('rights_add'));
            foreach ($toAdd as $right) {
                self::getRights()->addRight(trim($right));
            }
        }

        // Start the session
        self::getSession()->startSession();
    }

    /**
     * Load the modules and store their names in self::$modules
     */
    public static function loadModules() {
        $dir = self::getDir('app/modules') . DS . '*';
        foreach (glob($dir) as $filename) {
            $filenameArray = explode(DS, $filename);
            $moduleName = end($filenameArray);
            self::$modules[$moduleName] = [
                'name' => $moduleName,
                'path' => $filename,
            ];
        }
    }

    /**
     * Return the loaded modules
     *
     * @return array
     */
    public static function getModules() {
        return self::$modules;
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
     * Returns the view directory for either the given module or the current one
     *
     * @param string    $subPath
     * @param null      $module
     *
     * @return string|null
     */
    public static function getViewDir($subPath = null, $module = null) {
        if (!$module) {
            $module = self::$currentModule;
        }

        $dir = 'app' . DS . 'modules' . DS . $module . DS . 'view';
        if ($subPath) {
            $dir = $dir . DS . trim($subPath) . '.phtml';
        }
        return self::getDir($dir);
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
        self::$debugEnabled = true;
        ini_set('display_errors', '1');
    }

    /**
     * Disables debug mode, which will hide errors. Errors are always logged (see Bootstrap.php)
     */
    public static function disableDebug() {
        self::$debugEnabled = false;
        ini_set('display_errors', '0');
    }

    /**
     * Returns whether debug is enabled or not
     *
     * @return bool
     */
    public static function isDebugEnabled() {
        return (bool)self::$debugEnabled;
    }

    /**
     * Returns (and possibly instantiates) the Cli instance
     *
     * @return \Devvoh\Components\Cli
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
     * @return \Devvoh\Fluid\App\Config
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
     * @return \Devvoh\Components\Hook
     */
    public static function getHook() {
        if (!self::$hook) {
            self::$hook = new \Devvoh\Components\Hook();
        }
        return self::$hook;
    }

    /**
     * Returns (and possibly instantiates) the Dock instance
     *
     * @return \Devvoh\Components\Dock
     */
    public static function getDock() {
        if (!self::$dock) {
            self::$dock = new \Devvoh\Components\Dock();
        }
        return self::$dock;
    }

    /**
     * Returns (and possibly instantiates) the Log instance
     *
     * @return \Devvoh\Components\Log
     */
    public static function getLog() {
        if (!self::$log) {
            self::$log = new \Devvoh\Components\Log();
        }
        return self::$log;
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context session
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getSession() {
        if (!self::$session) {
            self::$session = (new \Devvoh\Components\GetSet())->setResource('session');
        }
        return self::$session;
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context param
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getParam() {
        if (!self::$param) {
            self::$param = (new \Devvoh\Components\GetSet())->setResource('param');
        }
        return self::$param;
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context post
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getPost() {
        if (!self::$post) {
            self::$post = (new \Devvoh\Components\GetSet())->setResource('post');
        }
        return self::$post;
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context get
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getGet() {
        if (!self::$get) {
            self::$get = (new \Devvoh\Components\GetSet())->setResource('get');
        }
        return self::$get;
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context sessionMessage
     *
     * @return \Devvoh\Components\SessionMessage
     */
    public static function getSessionMessage() {
        if (!self::$sessionMessage) {
            self::$sessionMessage = new \Devvoh\Components\SessionMessage();
        }
        return self::$sessionMessage;
    }

    /**
     * Returns (and possibly instantiates) the Router instance
     *
     * @return \Devvoh\Components\Router
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
     * @return \Devvoh\Components\Database
     */
    public static function getDatabase() {
        if (!self::$database) {
            self::$database = new \Devvoh\Components\Database();
        }
        return self::$database;
    }

    /**
     * Returns (and possibly instantiates) the Response instance
     *
     * @return \Devvoh\Components\Response
     */
    public static function getResponse() {
        if (!self::$response) {
            self::$response = new \Devvoh\Components\Response();
        }
        return self::$response;
    }

    /**
     * Returns (and possibly instantiates) the Debug instance
     *
     * @return \Devvoh\Components\Debug
     */
    public static function getDebug() {
        if (!self::$debug) {
            self::$debug = new \Devvoh\Components\Debug();
        }
        return self::$debug;
    }

    /**
     * Returns (and possibly instantiates) the View instance
     *
     * @return \Devvoh\Fluid\App\View
     */
    public static function getView() {
        if (!self::$view) {
            self::$view = new \Devvoh\Fluid\App\View();
        }
        return self::$view;
    }

    /**
     * Returns (and possibly instantiates) the Rights instance
     *
     * @return \Devvoh\Components\Rights
     */
    public static function getRights() {
        if (!self::$rights) {
            self::$rights = new \Devvoh\Components\Rights();
        }
        return self::$rights;
    }

    /**
     * Returns (and possibly instantiates) the Date instance
     *
     * @return \Devvoh\Components\Date
     */
    public static function getDate() {
        if (!self::$date) {
            self::$date = new \Devvoh\Components\Date();
        }
        return self::$date;
    }

    /**
     * Returns (and possibly instantiates) the Curl instance
     *
     * @return \Devvoh\Components\Curl
     */
    public static function getCurl() {
        if (!self::$curl) {
            self::$curl = new \Devvoh\Components\Curl();
        }
        return self::$curl;
    }

    /**
     * Returns (and possibly instantiates) the Validate instance
     *
     * @return \Devvoh\Components\Validate
     */
    public static function getValidate() {
        if (!self::$validate) {
            self::$validate = new \Devvoh\Components\Validate();
        }
        return self::$validate;
    }

    /**
     * Gets and returns the version stored in ./version
     *
     * @return string
     */
    public static function getVersion() {
        if (!self::$version) {
            self::$version = file_get_contents(self::getBaseDir() . DS . 'version');
        }
        return self::$version;
    }

    /**
     * Returns a new Query object, with the PDO instance set if possible
     *
     * @return \Devvoh\Components\Query
     */
    public static function createQuery() {
        $query = new \Devvoh\Components\Query();
        if (self::getDatabase()) {
            $query->setPdoInstance(self::getDatabase()->getInstance());
        }
        return $query;
    }

    /**
     * Returns a new Repository object
     *
     * @return \Devvoh\Components\Repository
     */
    public static function createRepository($entityName = null) {
        $repository = new \Devvoh\Fluid\Repository();

        // Build the proper entity name
        $entityName = '\\' . self::$currentModule . '\\Model\\' . $entityName;

        $entity = new $entityName();
        $repository->setEntity($entity);
        return $repository;
    }

    /**
     * Match the route using the router and store the result in self::$route
     *
     * @return null|array
     */
    public static function matchRoute() {
        self::setRoute(self::getRouter()->match());
        return self::getRoute();
    }

    /**
     * Set the route
     *
     * @param $route
     */
    public static function setRoute($route) {
        self::$route = $route;
    }

    /**
     * Returns the route
     *
     * @return null|array
     */
    public static function getRoute() {
        return self::$route;
    }

    /**
     * Collect routes and include the files, which will add their routes to the router automatically
     */
    public static function collectRoutes() {
        foreach (self::getModules() as $module) {
            $routerFilename = $module['path'] . DS . 'Routes.php';
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
    public static function executeRoute($route = null) {
        if (!$route) {
            $route = self::getRoute();
        }

        // Start a new level of output buffering to put whatever we're going to output into the Response
        ob_start();

        // Store the current module
        self::$currentModule = $route['module'];
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
                $viewTemplate = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'View' . DS . $route['view'] . '.phtml';
            }
        } else {
            // Not a closure, build a controller/action combination
            $classNameFull = '\\' . $route['module'] . '\\' . 'Controller' . '\\' . $route['controller'];
            $controllerFile = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'Controller' . DS . $route['controller'] . '.php';
            $viewTemplate = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'View' . DS . $route['controller'] . DS . $route['action'] . '.phtml';

            // Just in case our controllerFile or viewTemplate variables contains any backslashes, replace them with regular ones
            $controllerFile = str_replace('\\', '/', $controllerFile);
            $viewTemplate = str_replace('\\', '/', $viewTemplate);

            // And check whether the file exists before trying to instantiate it.
            if (file_exists($controllerFile)) {
                require_once($controllerFile);
                // Get all the data
                $controllerName = $route['controller'];
                $action         = $route['action'];
                $controller     = new $classNameFull();
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

        // If valid $viewTemplate is set, load it into the view
        if (isset($viewTemplate) && file_exists($viewTemplate)) {
            self::getView()->loadTemplate($viewTemplate);
        }

        // And get the output buffer and add it to the Response
        $return = ob_get_clean();
        App::getResponse()->appendContent($return);

        return true;
    }

    /**
     * Redirect to $url
     *
     * @return false
     */
    public static function redirect($url = null) {
        if (!$url) {
            return false;
        }
        if (strpos($url, 'http://') === false) {
            $url = self::getUrl($url);
        }
        header('location: ' . $url);
        exit;
    }

    /**
     * Redirect to route
     *
     * @return false
     */
    public static function redirectRoute($routeName = null, $params = null) {
        if (!$routeName) {
            return false;
        }
        if ($params && !is_array($params)) {
            $params = [$params];
        }
        $url = self::getRouter()->buildRoute($routeName, $params);
        return self::redirect(self::getUrl($url));
    }

}
