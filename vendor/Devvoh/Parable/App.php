<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class App {

    static protected $instance          = null;

    /**
     * @var null|string
     */
    protected $version           = null;

    /**
     * @var null|string
     */
    protected $baseDir           = null;

    /**
     * @var null|string
     */
    protected $publicUrl         = null;

    /**
     * @var bool
     */
    protected $debugEnabled      = false;

    /**
     * @var null|array
     */
    protected $route             = null;

    /**
     * @var null|string
     */
    protected $currentModule     = null;

    /**
     * @var array
     */
    protected $modules                  = [];

    /**
     * @var array
     */
    protected $singletons        = [];

    /**
     * Return singleton instance of ourself
     *
     * @return App|null
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set $instance as instance instead of ourself
     *
     * @param $instance
     */
    public static function setInstance($instance) {
        self::$instance = $instance;
    }

    /**
     * Prevent any new instance of this class from existing
     */
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Set the route
     *
     * @param $route
     *
     * @return $this
     */
    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }

    /**
     * Returns the route
     *
     * @return null|array
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Return the loaded modules
     *
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }

    /**
     * Returns the base directory for the application.
     *
     * @return null|string
     */
    public function getBaseDir() {
        if (!$this->baseDir) {
            $baseDir = rtrim(getcwd(), DS);
            $this->baseDir = rtrim($baseDir, 'public');
        }
        return $this->baseDir;
    }

    /**
     * Returns the public url for the application.
     *
     * @return null|string
     */
    public function getPublicUrl() {
        if (!$this->publicUrl) {
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

            $this->publicUrl = rtrim($url, '/');
        }
        return $this->publicUrl;
    }

    /**
     * Returns a dir based on the base dir
     *
     * @param null|string $path
     *
     * @return null|string
     */
    public function getDir($path = null) {
        $dir = $this->getBaseDir();
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
    public function getViewDir($subPath = null, $module = null) {
        if (!$module) {
            $module = $this->getCurrentModule();
        }

        $dir = 'app' . DS . 'modules' . DS . $module . DS . 'view';
        if ($subPath) {
            $dir = $dir . DS . trim($subPath) . '.phtml';
        }
        return $this->getDir($dir);
    }

    /**
     * Returns an url based on the public url
     *
     * @param null|string $path
     *
     * @return null|string
     */
    public function getUrl($path = null) {
        $url = $this->getPublicUrl();
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
    public function getCurrentUrl() {
        return $this->getUrl($_GET['path']);
    }

    /**
     * Enables debug mode, which will display errors. Errors are always logged (see Bootstrap.php)
     *
     * @return $this
     */
    public function enableDebug() {
        $this->debugEnabled = true;
        ini_set('display_errors', '1');
        return $this;
    }

    /**
     * Disables debug mode, which will hide errors. Errors are always logged (see Bootstrap.php)
     *
     * @return $this
     */
    public function disableDebug() {
        $this->debugEnabled = false;
        ini_set('display_errors', '0');
        return $this;
    }

    /**
     * Returns whether debug is enabled or not
     *
     * @return bool
     */
    public function isDebugEnabled() {
        return (bool)$this->debugEnabled;
    }

    /**
     * Returns the current module
     *
     * @return null|string
     */
    public function getCurrentModule() {
        return $this->currentModule;
    }

    /**
     * Set the current module
     *
     * @param $currentModule
     *
     * @return $this
     */
    public function setCurrentModule($currentModule) {
        $this->currentModule = $currentModule;
        return $this;
    }

    /**
     * Return the module name based on the given $path
     *
     * @param string $path
     * @return mixed|null
     */
    public function getModuleFromPath($path = null) {
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
    public function getVersion() {
        if (!$this->version) {
            $this->version = trim(file_get_contents($this->getBaseDir() . DS . 'version'));
        }
        return $this->version;
    }

    /**
     * Starts the App class and does some initial setup
     *
     * @return $this
     * @throws \Exception
     */
    public function boot()
    {
        $this->getResponse()->startOB();

        // Find out what modules we have
        $this->loadModules();

        // Collect the routes, now that we know our modules
        $this->collectRoutes();

        // And load the App config
        $this->getConfig()->load();

        // Set debug enabled/disabled based on config
        if ($this->getConfig()->get('debug_enabled')) {
            $this->enableDebug();
            $this->getDebug()->startTimer();
        } else {
            $this->disableDebug();
        }

        // Set and enable database based on config
        if ($this->getConfig()->get('storage_type') && $this->getConfig()->get('storage_location')) {
            $config = [
                'type' => $this->getConfig()->get('storage_type'),
                'location' => $this->getConfig()->get('storage_location'),
                'username' => $this->getConfig()->get('storage_username'),
                'password' => $this->getConfig()->get('storage_password'),
                'database' => $this->getConfig()->get('storage_database'),
            ];
            $this->getDatabase()->setConfig($config);
        }

        // Set timezone if given, otherwise default to Europe/London
        $this->getDate()->setTimezone('Europe/London');
        if ($this->getConfig()->get('default_timezone')) {
            $this->getDate()->setTimezone(
                $this->getConfig()->get('default_timezone')
            );
        }

        // Set the appropriate log directory & default file name
        $this->getLog()->setPath($this->getBaseDir() . 'var' . DS . 'log');

        // And see if there's additional rights levels we should add
        if ($this->getConfig()->get('rights_add')) {
            $toAdd = explode(',', $this->getConfig()->get('rights_add'));
            foreach ($toAdd as $right) {
                $this->getRights()->addRight(trim($right));
            }
        }

        // Start the session
        $this->getSession()->startSession();

        // Load all module Init scripts
        $this->loadModuleInits();

        return $this;
    }

    /**
     * Dispatch the current route
     *
     * @return $this
     */
    public function dispatch() {
        $this->getHook()->trigger('parable_app_dispatch_before');
        // Try to match the path to an existing route. If no path given to ->route(), current $_GET value is used.
        $matchedRoute = $this->matchRoute();
        if ($matchedRoute) {
            // Create the dispatcher and try to dispatch
            $dispatcher = $this->createDispatcher($matchedRoute);
            if (!$dispatcher->dispatch()) {
                $this->getResponse()->appendContent(
                    $this->getView()->partial('Error/Route.phtml')
                );
            }
        } else {
            $this->getResponse()->appendContent(
                $this->getView()->partial('Error/404.phtml')
            );
        }

        // Last thing we do is ask our Response to send it all as configured
        $this->getHook()->trigger('parable_app_response_sendResponse_before');
        $this->getResponse()->sendResponse();
        $this->getHook()->trigger('parable_app_response_sendResponse_after');
        $this->getHook()->trigger('parable_app_dispatch_after');

        return $this;
    }

    /**
     * Load the modules and store their names in $this->modules
     *
     * @return $this
     */
    public function loadModules() {
        $this->getHook()->trigger('parable_app_loadModules_before');
        $dirIt = new \RecursiveDirectoryIterator($this->getDir('app/modules'), \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($dirIt as $file) {
            $this->modules[$file->getFileName()] = [
                'name' => $file->getFileName(),
                'path' => $file->getPathName(),
            ];
        }
        $this->getHook()->trigger('parable_app_loadModules_after');

        return $this;
    }

    /**
     * Where applicable, load all scripts in app/modules/APPNAME/Init
     *
     * @return $this
     */
    public function loadModuleInits() {
        foreach ($this->getModules() as $module) {
            // Build init path for this module
            $initPath = $module['path'] . DS . 'Init';
            // If there's no init path, just go onto the next module
            if (!file_exists($initPath)) {
                continue;
            }
            // Generate an iterator for our files
            $dirIt = new \RecursiveDirectoryIterator($initPath, \RecursiveDirectoryIterator::SKIP_DOTS);
            foreach ($dirIt as $file) {
                // Skip non-php files
                if (strpos($file->getFileName(), '.php') === false) {
                    continue;
                }
                // Generate the class to instantiate
                $className = str_replace('.php', '', $file->getFileName());
                $className = '\\' . $module['name'] . '\\Init\\' . $className;
                // And instantiate it
                new $className();
            }
        }

        return $this;
    }

    /**
     * Returns a new Query object, with the PDO instance set if possible
     *
     * @return \Devvoh\Components\Query
     */
    public function createQuery() {
        $query = new \Devvoh\Components\Query();
        if ($this->getDatabase()) {
            $query->setPdoInstance($this->getDatabase()->getInstance());
            $query->setQuoteAll($this->getDatabase()->getQuoteAll());
        }
        return $query;
    }

    /**
     * Returns a new Repository instance
     *
     * @param null $entityName
     *
     * @return \Devvoh\Parable\Repository
     */
    public function createRepository($entityName = null) {
        $repository = new \Devvoh\Parable\Repository();

        $entity = $this->createEntity($entityName);
        $repository->setEntity($entity);

        return $repository;
    }

    /**
     * Returns a new Entity instance
     *
     * @param null|string $entityName
     * @return Entity
     */
    public function createEntity($entityName = null) {
        $entity = new \Devvoh\Parable\Entity();
        // Loop through models trying to find the appropriate class
        foreach ($this->getModules() as $module) {
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
    public function createDispatcher($route = null) {
        $dispatcher = new \Devvoh\Parable\Dispatcher($route);
        return $dispatcher;
    }

    /**
     * Match the route using the router and store the result with $this->setRoute()
     *
     * @return null|array
     */
    public function matchRoute() {
        $this->getHook()->trigger('parable_app_router_match_before');
        $this->setRoute($this->getRouter()->match());
        $this->getHook()->trigger('parable_app_router_match_after');
        return $this->getRoute();
    }

    /**
     * Collect routes and include the files, which will add their routes to the router automatically
     */
    public function collectRoutes() {
        foreach ($this->getModules() as $module) {
            $routerFilename = $module['path'] . DS . 'Routes.php';
            if (file_exists($routerFilename)) {
                require_once($routerFilename);
            }
        }
        return $this;
    }

    /**
     * Returns (and possibily creates an instance of the $className class)
     *
     * @param $className
     *
     * @return mixed
     */
    protected function getSingleton($className) {
        if (!isset($this->singletons[$className])) {
            $this->singletons[$className] = new $className();
        }
        return $this->singletons[$className];
    }

    /**
     * Returns (and possibly instantiates) the Cli instance
     *
     * @return \Devvoh\Components\Cli
     */
    public function getCli() {
        return $this->getSingleton('\Devvoh\Components\Cli');
    }

    /**
     * Returns (and possibly instantiates) the Config instance
     *
     * @return \Devvoh\Parable\Config
     */
    public function getConfig() {
        return $this->getSingleton('\Devvoh\Parable\Config');
    }

    /**
     * Returns (and possibly instantiates) the Hook instance
     *
     * @return \Devvoh\Components\Hook
     */
    public function getHook() {
        return $this->getSingleton('\Devvoh\Components\Hook');
    }

    /**
     * Returns (and possibly instantiates) the Dock instance
     *
     * @return \Devvoh\Components\Dock
     */
    public function getDock() {
        return $this->getSingleton('\Devvoh\Components\Dock');
    }

    /**
     * Returns (and possibly instantiates) the Log instance
     *
     * @return \Devvoh\Components\Log
     */
    public function getLog() {
        return $this->getSingleton('\Devvoh\Components\Log');
    }

    /**
     * Returns (and possibly instantiates) the Mailer instance
     *
     * @return \Devvoh\Components\Mailer
     */
    public function getMailer() {
        return $this->getSingleton('\Devvoh\Components\GetSet');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context session
     *
     * @return \Devvoh\Components\GetSet
     */
    public function getSession() {
        return $this->getSingleton('\Devvoh\Components\GetSet')->setResource('session');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context param
     *
     * @return \Devvoh\Components\GetSet
     */
    public function getParam() {
        return $this->getSingleton('\Devvoh\Components\GetSet')->setResource('param');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context post
     *
     * @return \Devvoh\Components\GetSet
     */
    public function getPost() {
        return $this->getSingleton('\Devvoh\Components\GetSet')->setResource('post');
    }

    /**
     * Returns (and possibly instantiates) the GetSet instance with context get
     *
     * @return \Devvoh\Components\GetSet
     */
    public function getGet() {
        return $this->getSingleton('\Devvoh\Components\GetSet')->setResource('get');
    }

    /**
     * Returns (and possibly instantiates) the sessionMessage instance
     *
     * @return \Devvoh\Components\SessionMessage
     */
    public function getSessionMessage() {
        return $this->getSingleton('\Devvoh\Components\SessionMessage');
    }

    /**
     * Returns (and possibly instantiates) the Router instance
     *
     * @return \Devvoh\Components\Router
     */
    public function getRouter() {
        return $this->getSingleton('\Devvoh\Components\Router');
    }

    /**
     * Returns (and possibly instantiates) the Database instance
     *
     * @return \Devvoh\Components\Database
     */
    public function getDatabase() {
        return $this->getSingleton('\Devvoh\Components\Database');
    }

    /**
     * Returns (and possibly instantiates) the Response instance
     *
     * @return \Devvoh\Components\Response
     */
    public function getResponse() {
        return $this->getSingleton('\Devvoh\Components\Response');
    }

    /**
     * Returns (and possibly instantiates) the Debug instance
     *
     * @return \Devvoh\Components\Debug
     */
    public function getDebug() {
        return $this->getSingleton('\Devvoh\Components\Debug');
    }

    /**
     * Returns (and possibly instantiates) the View instance
     *
     * @return \Devvoh\Parable\View
     */
    public function getView() {
        return $this->getSingleton('\Devvoh\Parable\View');
    }

    /**
     * Returns (and possibly instantiates) the Rights instance
     *
     * @return \Devvoh\Components\Rights
     */
    public function getRights() {
        return $this->getSingleton('\Devvoh\Components\Rights');
    }

    /**
     * Returns (and possibly instantiates) the Date instance
     *
     * @return \Devvoh\Components\Date
     */
    public function getDate() {
        return $this->getSingleton('\Devvoh\Components\Date');
    }

    /**
     * Returns (and possibly instantiates) the Curl instance
     *
     * @return \Devvoh\Components\Curl
     */
    public function getCurl() {
        return $this->getSingleton('\Devvoh\Components\Curl');
    }

    /**
     * Returns (and possibly instantiates) the Validate instance
     *
     * @return \Devvoh\Components\Validate
     */
    public function getValidate() {
        return $this->getSingleton('\Devvoh\Components\Validate');
    }

    /**
     * Returns (and possibly instantiates) the Tool instance
     *
     * @return \Devvoh\Components\Tool
     */
    public function getTool() {
        return $this->getSingleton('\Devvoh\Parable\Tool');
    }

}
