<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class App {

    /**
     * @var null|string
     */
    static protected $version           = null;

    /**
     * @var null|string
     */
    static protected $baseDir           = null;

    /**
     * @var null|string
     */
    static protected $publicUrl         = null;

    /**
     * @var bool
     */
    static protected $debugEnabled      = false;

    /**
     * @var null|\Devvoh\Components\Cli
     */
    static protected $cli               = null;

    /**
     * @var null|\Devvoh\Parable\App\Config
     */
    static protected $config            = null;

    /**
     * @var null|\Devvoh\Components\Hook
     */
    static protected $hook              = null;

    /**
     * @var null|\Devvoh\Components\Dock
     */
    static protected $dock              = null;

    /**
     * @var null|\Devvoh\Components\Log
     */
    static protected $log               = null;

    /**
     * @var null|\Devvoh\Components\Mailer
     */
    static protected $mailer            = null;

    /**
     * @var null|\Devvoh\Components\GetSet
     */
    static protected $param             = null;

    /**
     * @var null|\Devvoh\Components\GetSet
     */
    static protected $post              = null;

    /**
     * @var null|\Devvoh\Components\GetSet
     */
    static protected $get               = null;

    /**
     * @var null|\Devvoh\Components\GetSet
     */
    static protected $session           = null;

    /**
     * @var null|\Devvoh\Components\SessionMessage
     */
    static protected $sessionMessage    = null;

    /**
     * @var null|\Devvoh\Components\Response
     */
    static protected $response          = null;

    /**
     * @var null|\Devvoh\Components\Router
     */
    static protected $router            = null;

    /**
     * @var null|\Devvoh\Components\Database
     */
    static protected $database          = null;

    /**
     * @var null|array
     */
    static protected $route             = null;

    /**
     * @var null|\Devvoh\Components\Debug
     */
    static protected $debug             = null;

    /**
     * @var null|\Devvoh\Parable\App\View
     */
    static protected $view              = null;

    /**
     * @var null|\Devvoh\Components\Rights
     */
    static protected $rights            = null;

    /**
     * @var null|\Devvoh\Components\Date
     */
    static protected $date              = null;

    /**
     * @var null|\Devvoh\Components\Curl
     */
    static protected $curl              = null;

    /**
     * @var null|\Devvoh\Components\Validate
     */
    static protected $validate          = null;

    /**
     * @var null|string
     */
    static protected $currentModule     = null;

    /**
     * @var array
     */
    static protected $modules           = [];

    /**
     * Starts the App class and does some initial setup
     */
    public static function boot()
    {
        self::getResponse()->startOB();

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
                'type' => self::getConfig()->get('storage_type'),
                'location' => self::getConfig()->get('storage_location'),
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
     * Run the app
     */
    public static function run() {
        // Try to match the path to an existing route. If no path given to ->route(), current $_GET value is used.
        if (self::matchRoute()) {
            if (!self::executeRoute()) {
                echo self::getView()->partial('Error/Route.phtml');
            }
        } else {
            echo self::getView()->partial('Error/404.phtml');
        }

        // Last thing we do is ask our Response to send it all as configured
        self::getResponse()->sendResponse();
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
            $module = self::getCurrentModule();
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
     * Returns the current url
     *
     * @return null|string
     */
    public static function getCurrentUrl() {
        return self::getUrl($_GET['path']);
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
     * @return \Devvoh\Parable\App\Config
     */
    public static function getConfig() {
        if (!self::$config) {
            self::$config = new \Devvoh\Parable\App\Config();
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
     * Returns (and possibly instantiates) the Mailer instance
     *
     * @return \Devvoh\Components\Mailer
     */
    public static function getMailer() {
        if (!self::$mailer) {
            self::$mailer = new \Devvoh\Components\Mailer();
        }
        return self::$mailer;
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
     * @return \Devvoh\Parable\App\View
     */
    public static function getView() {
        if (!self::$view) {
            self::$view = new \Devvoh\Parable\App\View();
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
     * Returns the current module
     *
     * @return null|string
     */
    public static function getCurrentModule() {
        return self::$currentModule;
    }

    /**
     * Set the current module
     *
     * @param $currentModule
     */
    public static function setCurrentModule($currentModule) {
        self::$currentModule = $currentModule;
    }

    /**
     * Return the module name based on the given $path
     *
     * @param string $path
     * @return mixed|null
     */
    public static function getModuleFromPath($path = null) {
        if (!$path) {
            return null;
        }

        $parts = explode(DS, $path);
        $modulePart = array_pop($parts);
        $moduleRoot = array_pop($parts);

        if ($moduleRoot === 'modules') {
            return $modulePart;
        }
        return null;
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
            self::$version = trim(file_get_contents(self::getBaseDir() . DS . 'version'));
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
            $query->setQuoteAll(self::getDatabase()->getQuoteAll());
        }
        return $query;
    }

    /**
     * Returns a new Repository object
     *
     * @param null $entityName
     *
     * @return \Devvoh\Parable\Repository
     */
    public static function createRepository($entityName = null) {
        $repository = new \Devvoh\Parable\Repository();

        $entity = self::createEntity($entityName);
        $repository->setEntity($entity);

        return $repository;
    }

    public static function createEntity($entityName = null) {
        $entity = new \Devvoh\Parable\Entity();
        // Loop through models trying to find the appropriate class
        foreach (self::getModules() as $module) {
            $entityNameComplete = '\\' . $module['name'] . '\\Model\\' . $entityName;
            if (class_exists($entityNameComplete)) {
                $entity = new $entityNameComplete();
                break;
            }
        }
        return $entity;
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
        self::getResponse()->startOB();

        // Store the current module
        self::setCurrentModule($route['module']);
        // Check for params
        if (isset($route['params'])) {
            foreach ($route['params'] as $param) {
                if (isset($param['name']) && isset($param['value'])) {
                    self::getParam()->set($param['name'], $param['value']);
                }
            }
        }

        // Check for view param in the router definition. This is only used in case of closures or if the auto-
        // generated view template doesn't exist.
        $viewTemplateRoute = null;
        if (isset($route['view'])) {
            $viewTemplateRoute = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'View' . DS . $route['view'];
        }

        $viewTemplate = null;
        // Check for a closure and set the result to $closureReturn if valid
        $closureReturn = null;
        if (isset($route['closure'])) {
            $closure = $route['closure'];
            // Check if we can call it
            if (is_callable($closure)) {
                // Call it
                $closureReturn = $closure();
            } else {
                // Not callable, so false
                return false;
            }
        } else {
            // Not a closure, build a controller/action combination
            $classNameFull = '\\' . $route['module'] . '\\' . 'Controller' . '\\' . $route['controller'];
            $controllerFile = self::getBaseDir() . 'app/modules' . str_replace('\\', DS, $classNameFull) . '.php';
            $viewTemplate = self::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'View' . DS . $route['controller'] . DS . $route['action'] . '.phtml';

            // Just in case our controllerFile or viewTemplate variables contains any backslashes, replace them with regular ones
            $controllerFile = str_replace('\\', '/', $controllerFile);
            $viewTemplate = str_replace('\\', '/', $viewTemplate);

            // And check whether the file exists before trying to instantiate it.
            if (file_exists($controllerFile)) {
                // Get all the data
                $action         = $route['action'];
                $controller     = new $classNameFull();
                // And call the action if it exists
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    // Invalid action
                    return false;
                }
            } else {
                // Invalid controller
                return false;
            }
        }

        if ($closureReturn) {
            self::getResponse()->appendContent($closureReturn);
        }

        // If valid $viewTemplate is set, load it into the view
        if (!$viewTemplateRoute && $viewTemplate && file_exists($viewTemplate)) {
            self::getView()->loadTemplate($viewTemplate);
        } elseif ($viewTemplateRoute && file_exists($viewTemplateRoute)) {
            self::getView()->loadTemplate($viewTemplateRoute);
        }

        // And get the output buffer contents and add it to the Response
        echo self::getResponse()->endOB();

        return true;
    }

    /**
     * Redirect to $url
     *
     * @param null $url
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
     * @param null $routeName
     * @param null $params
     *
     * @return bool|false
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

    public static function end($message = null) {
        exit($message);
    }

}
