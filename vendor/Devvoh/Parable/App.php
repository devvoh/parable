<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class App {

    /** @var string */
    static protected $version           = '0.5.0';

    /** @var null|string */
    static protected $baseDir           = null;

    /** @var null|string */
    static protected $publicUrl         = null;

    /** @var bool */
    static protected $debugEnabled      = false;

    /** @var null|array */
    static protected $route             = null;

    /** @var null|string */
    static protected $currentModule     = null;

    /** @var array */
    static protected $modules           = [];

    /** @var array */
    static protected $singletons        = [];

    /**
     * Set the route
     *
     * @param array $route
     */
    public static function setRoute(array $route) {
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

        if ($moduleRoot !== 'modules') {
            return null;
        }
        return $modulePart;
    }

    /**
     * Gets and returns the version stored in ./version
     *
     * @return string
     */
    public static function getVersion() {
        return self::$version;
    }

    /**
     * Starts the App class and does some initial setup
     *
     * @throws \Exception
     */
    public static function boot()
    {
        self::getResponse()->startOB();

        // Find out what modules we have
        self::loadModules();

        // Collect the routes, now that we know our modules
        self::collectRoutes();

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
            $location = self::getConfig()->get('storage_location');
            if (strpos(self::getConfig()->get('storage_type'), 'sqlite') !== false) {
                $location = self::getDir(self::getConfig()->get('storage_location'));
            }
            $config = [
                'type'      => self::getConfig()->get('storage_type'),
                'location'  => $location,
                'username'  => self::getConfig()->get('storage_username'),
                'password'  => self::getConfig()->get('storage_password'),
                'database'  => self::getConfig()->get('storage_database'),
            ];
            self::getDatabase()->setConfig($config);
        }

        // Set timezone if given, otherwise default to Europe/London
        self::getDate()->setTimezone('Europe/London');
        if (self::getConfig()->get('default_timezone')) {
            self::getDate()->setTimezone(
                self::getConfig()->get('default_timezone')
            );
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

        // Load all module Init scripts
        self::getInit()->run();
    }

    /**
     * Dispatch the current route
     */
    public static function dispatch() {
        self::getHook()->trigger('parable_app_dispatch_before');
        // Try to match the path to an existing route. If no path given to ->route(), current $_GET value is used.
        $matchedRoute = self::matchRoute();
        $dispatched = false;
        if ($matchedRoute) {
            // Create the dispatcher and try to dispatch
            $dispatcher = self::createDispatcher($matchedRoute);
            $dispatched = $dispatcher->dispatch();
        }

        if (!$dispatched) {
            $template = self::getView()->partial('Error/404.phtml');
            if (!$template) {
                $template = '404: Page Not Found';
            }
            self::getResponse()->setContent($template);
        }

        // Last thing we do is ask our Response to send it all as configured
        self::getHook()->trigger('parable_app_response_sendResponse_before');
        self::getResponse()->sendResponse();
        self::getHook()->trigger('parable_app_response_sendResponse_after');
        self::getHook()->trigger('parable_app_dispatch_after');
    }

    /**
     * Load the modules and store their names in self::$modules
     */
    public static function loadModules() {
        self::getHook()->trigger('parable_app_loadModules_before');
        $dirIt = new \RecursiveDirectoryIterator(
            self::getDir('app/modules'),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        foreach ($dirIt as $file) {
            self::$modules[$file->getFileName()] = [
                'name' => $file->getFileName(),
                'path' => $file->getPathName(),
            ];
        }
        self::getHook()->trigger('parable_app_loadModules_after');
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
     * Returns a new Repository instance
     *
     * @param null $entityName
     * @return \Devvoh\Parable\Repository
     */
    public static function createRepository($entityName = null) {
        $repository = new \Devvoh\Parable\Repository();

        $entity = self::createEntity($entityName);
        $repository->setEntity($entity);

        return $repository;
    }

    /**
     * Returns a new Entity instance
     *
     * @param null|string $entityName
     * @return Entity
     */
    public static function createEntity($entityName = null) {
        $entity = null;
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
     * Return a new Dispatcher instance
     *
     * @param null|array $route
     * @return Dispatcher
     */
    public static function createDispatcher($route = null) {
        $dispatcher = new \Devvoh\Parable\Dispatcher($route);
        return $dispatcher;
    }

    /**
     * Match the route using the router and store the result with self::setRoute()
     *
     * @return null|array
     */
    public static function matchRoute() {
        self::getHook()->trigger('parable_app_router_match_before');
        self::setRoute(
            self::getRouter()->match()
        );
        self::getHook()->trigger('parable_app_router_match_after');
        return self::getRoute();
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
     * Returns (and possibly creates an instance of the $className class)
     *
     * @param $className
     * @param null $singletonName
     * @return mixed
     */
    protected static function getSingleton($className, $singletonName = null) {
        if (!$singletonName) {
            $singletonName = $className;
        }
        if (!isset(self::$singletons[$singletonName])) {
            self::$singletons[$singletonName] = new $className();
            self::$singletons[$singletonName]->time = time();
        }
        return self::$singletons[$singletonName];
    }

    /**
     * Returns (and possibly instantiates) the Cli instance
     *
     * @return \Devvoh\Components\Cli
     */
    public static function getCli() {
        return self::getSingleton('\Devvoh\Components\Cli');
    }

    /**
     * Returns (and possibly instantiates) the Config instance
     *
     * @return \Devvoh\Parable\Config
     */
    public static function getConfig() {
        return self::getSingleton('\Devvoh\Parable\Config');
    }

    /**
     * Returns (and possibly instantiates) the Hook instance
     *
     * @return \Devvoh\Components\Hook
     */
    public static function getHook() {
        return self::getSingleton('\Devvoh\Components\Hook');
    }

    /**
     * Returns (and possibly instantiates) the Dock instance
     *
     * @return \Devvoh\Components\Dock
     */
    public static function getDock() {
        return self::getSingleton('\Devvoh\Components\Dock');
    }

    /**
     * Returns (and possibly instantiates) the Log instance
     *
     * @return \Devvoh\Components\Log
     */
    public static function getLog() {
        return self::getSingleton('\Devvoh\Components\Log');
    }

    /**
     * Returns (and possibly instantiates) the Mailer instance
     *
     * @return \Devvoh\Components\Mailer
     */
    public static function getMailer() {
        return self::getSingleton('\Devvoh\Components\Mailer');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context session
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getCookies() {
        return self::getSingleton('\Devvoh\Components\GetSet', 'GetSetCookies')->setResource('cookie');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context session
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getSession() {
        return self::getSingleton('\Devvoh\Components\GetSet', 'GetSetSession')->setResource('session');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context param
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getParam() {
        return self::getSingleton('\Devvoh\Components\GetSet', 'GetSetParam')->setResource('param');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context post
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getPost() {
        return self::getSingleton('\Devvoh\Components\GetSet', 'GetSetPost')->setResource('post');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context get
     *
     * @return \Devvoh\Components\GetSet
     */
    public static function getGet() {
        return self::getSingleton('\Devvoh\Components\GetSet', 'GetSetGet')->setResource('get');
    }

    /**
     * Returns (and possibly instantiates) the sessionMessage instance
     *
     * @return \Devvoh\Components\SessionMessage
     */
    public static function getSessionMessage() {
        return self::getSingleton('\Devvoh\Components\SessionMessage');
    }

    /**
     * Returns (and possibly instantiates) the Router instance
     *
     * @return \Devvoh\Components\Router
     */
    public static function getRouter() {
        return self::getSingleton('\Devvoh\Components\Router');
    }

    /**
     * Returns (and possibly instantiates) the Database instance
     *
     * @return \Devvoh\Components\Database
     */
    public static function getDatabase() {
        return self::getSingleton('\Devvoh\Components\Database');
    }

    /**
     * Returns (and possibly instantiates) the Response instance
     *
     * @return \Devvoh\Components\Response
     */
    public static function getResponse() {
        return self::getSingleton('\Devvoh\Components\Response');
    }

    /**
     * Returns (and possibly instantiates) the Request instance
     *
     * @return \Devvoh\Components\Request
     */
    public static function getRequest() {
        return self::getSingleton('\Devvoh\Components\Request');
    }

    /**
     * Returns (and possibly instantiates) the Debug instance
     *
     * @return \Devvoh\Components\Debug
     */
    public static function getDebug() {
        return self::getSingleton('\Devvoh\Components\Debug');
    }

    /**
     * Returns (and possibly instantiates) the View instance
     *
     * @return \Devvoh\Parable\View
     */
    public static function getView() {
        return self::getSingleton('\Devvoh\Parable\View');
    }

    /**
     * Returns (and possibly instantiates) the Rights instance
     *
     * @return \Devvoh\Components\Rights
     */
    public static function getRights() {
        return self::getSingleton('\Devvoh\Components\Rights');
    }

    /**
     * Returns (and possibly instantiates) the Date instance
     *
     * @return \Devvoh\Components\Date
     */
    public static function getDate() {
        return self::getSingleton('\Devvoh\Components\Date');
    }

    /**
     * Returns (and possibly instantiates) the Curl instance
     *
     * @return \Devvoh\Components\Curl
     */
    public static function getCurl() {
        return self::getSingleton('\Devvoh\Components\Curl');
    }

    /**
     * Returns (and possibly instantiates) the Validate instance
     *
     * @return \Devvoh\Components\Validate
     */
    public static function getValidate() {
        return self::getSingleton('\Devvoh\Components\Validate');
    }

    /**
     * Returns (and possibly instantiates) the Tool instance
     *
     * @return \Devvoh\Parable\Tool
     */
    public static function getTool() {
        return self::getSingleton('\Devvoh\Parable\Tool');
    }

    /**
     * Returns (and possibly instantiates) the Auth instance
     *
     * @return \Devvoh\Parable\Auth
     */
    public static function getAuth() {
        return self::getSingleton('\Devvoh\Parable\Auth');
    }

    /**
     * Returns (and possibly instantiates) the Init instance
     *
     * @return \Devvoh\Parable\Init
     */
    public static function getInit() {
        return self::getSingleton('\Devvoh\Parable\Init');
    }

}
