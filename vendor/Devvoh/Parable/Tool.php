<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Tool {

    /** @var \Devvoh\Components\Autoloader */
    protected $autoloader;

    /** @var \Devvoh\Components\Router */
    protected $router;

    /** @var \Devvoh\Components\Database */
    protected $database;

    /** @var string */
    protected $version     = '0.7.0';

    /** @var array */
    protected $route       = [];

    /** @var array */
    protected $modules     = [];

    /** @var array */
    protected $resourceMap = [];

    /** @var string */
    protected $publicUrl;

    /** @var string */
    protected $baseDir;

    /** @var bool */
    protected $debugEnabled;

    /** @var string */
    protected $currentModule;

    /**
     * @param \Devvoh\Components\Autoloader $autoloader
     * @param \Devvoh\Components\Router     $router
     * @param \Devvoh\Components\Database   $database
     */
    public function __construct(
        \Devvoh\Components\Autoloader $autoloader,
        \Devvoh\Components\Router     $router,
        \Devvoh\Components\Database   $database
    ) {
        $this->autoloader = $autoloader;
        $this->router     = $router;
        $this->database   = $database;
    }

    /**
     * Redirect to $url or the root url
     *
     * @param string $url
     *
     * @return void
     */
    public function redirect($url = '/') {
        if (strpos($url, 'http://') === false) {
            $url = $this->getUrl($url);
        }
        header('location: ' . $url);
        $this->end();
    }

    /**
     * Redirect to route
     *
     * @param string $routeName
     * @param array  $params
     *
     * @return void
     */
    public function redirectRoute($routeName, array $params = []) {
        $url = $this->router->buildRoute($routeName, $params);
        return $this->redirect($this->getUrl($url));
    }

    /**
     * End program execution immediately
     *
     * @param null|string|int $message
     */
    public function end($message = null) {
        exit($message);
    }

    /**
     * Set the route
     *
     * @param array $route
     *
     * @return $this
     */
    public function setRoute(array $route) {
        $this->route = $route;
        return $this;
    }

    /**
     * Returns the route
     *
     * @return array
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
     * @return string
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
     * @return string
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
     * @param string $path
     *
     * @return string
     */
    public function getDir($path) {
        $dir = $this->getBaseDir();
        if ($path) {
            $dir = rtrim($dir, DS) . DS . trim($path, DS);
        }
        return $dir;
    }

    /**
     * Returns the view directory for either the given module or the current one
     *
     * @param null|string $subPath
     * @param null|string $module
     *
     * @return string
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
     * @return string
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
     * @return string
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
     * @return string
     */
    public function getCurrentModule() {
        return $this->currentModule;
    }

    /**
     * Set the current module
     *
     * @param string $currentModule
     *
     * @return $this
     */
    public function setCurrentModule($currentModule) {
        $this->currentModule = $currentModule;
        return $this;
    }

    /**
     * Add a module
     *
     * @param string $name
     * @param array  $data
     *
     * @return $this
     */
    public function addModule($name, array $data) {
        $this->modules[$name] = $data;
        return $this;
    }

    /**
     * Return the module name based on the given $path
     *
     * @param string $path
     *
     * @return null|mixed
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
        return $this->version;
    }

    /**
     * Returns a new Query object, with the PDO instance set if possible
     *
     * @return \Devvoh\Components\Query
     */
    public function createQuery() {
        $query = new \Devvoh\Components\Query();
        if ($this->database->getInstance()) {
            $query->setPdoInstance($this->database->getInstance());
            $query->setQuoteAll($this->database->getQuoteAll());
        }
        return $query;
    }

    /**
     * Returns a new Repository instance
     *
     * @param string $entityName
     *
     * @return \Devvoh\Parable\Repository
     */
    public function createRepository($entityName) {
        $repository = \Devvoh\Components\DI::create(\Devvoh\Parable\Repository::class);
        $entity = $this->createEntity($entityName);
        $repository->setEntity($entity);

        return $repository;
    }

    /**
     * Returns a new Entity instance
     *
     * @param string $entityName
     *
     * @return null|Entity
     */
    public function createEntity($entityName) {
        $entity = null;
        // Loop through models trying to find the appropriate class
        foreach ($this->getModules() as $module) {
            $entityNameComplete = '\\' . $module['name'] . '\\Model\\' . $entityName;
            if (class_exists($entityNameComplete)) {
                $entity = \Devvoh\Components\DI::create($entityNameComplete);
                break;
            }
        }
        return $entity;
    }

    /**
     * Load the resource map
     *
     * @return $this
     */
    public function loadResourceMap() {
        $dirIterator = new \RecursiveDirectoryIterator($this->getDir('vendor'), \RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator = new \RecursiveIteratorIterator($dirIterator);
        foreach ($iteratorIterator as $path => $file) {
            /** @var \SplFileInfo $file */
            $fullClassName = str_replace($this->getDir('vendor') . '/', null, $path);
            $fullClassName = str_replace('.' . $file->getExtension(), null, $fullClassName);
            $fullClassName = str_replace('/', '\\', $fullClassName);

            $classParts = explode('\\', $fullClassName);
            $className = end($classParts);

            $this->resourceMap[$className] = $fullClassName;
        }
        return $this;
    }

    /**
     * Return a mapping
     *
     * @param string $index
     *
     * @return null|string
     */
    public function getResourceMapping($index) {
        if (isset($this->resourceMap[$index])) {
            return $this->resourceMap[$index];
        }
        return null;
    }

}
