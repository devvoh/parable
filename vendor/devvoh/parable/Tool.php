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
     * Redirect to $url
     *
     * @param null|string $url
     * @return false|void
     */
    public function redirect($url = null) {
        if (!$url) {
            return false;
        }
        if (strpos($url, 'http://') === false) {
            $url = $this->getUrl($url);
        }
        header('location: ' . $url);
        return $this->end();
    }

    /**
     * Redirect to route
     *
     * @param null|string $routeName
     * @param array $params
     * @return false|void
     */
    public function redirectRoute($routeName = null, array $params = []) {
        if (!$routeName) {
            return false;
        }
        $url = $this->router->buildRoute($routeName, $params);
        return $this->redirect($this->getUrl($url));
    }

    /**
     * End program execution immediately
     *
     * @param null|string $message
     */
    public function end($message = null) {
        exit($message);
    }

    /**
     * Set the route
     *
     * @param null|array $route
     */
    public function setRoute($route) {
        $this->route = $route;
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
     * @return null|string
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
     */
    public function enableDebug() {
        $this->debugEnabled = true;
        ini_set('display_errors', '1');
    }

    /**
     * Disables debug mode, which will hide errors. Errors are always logged (see Bootstrap.php)
     */
    public function disableDebug() {
        $this->debugEnabled = false;
        ini_set('display_errors', '0');
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
     */
    public function setCurrentModule($currentModule) {
        $this->currentModule = $currentModule;
    }

    public function addModule($name, $data) {
        $this->modules[$name] = $data;
    }

    /**
     * Return the module name based on the given $path
     *
     * @param string $path
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
     * @param null $entityName
     * @return \Devvoh\Parable\Repository
     */
    public function createRepository($entityName = null) {
        $repository = \Devvoh\Components\DI::create(\Devvoh\Parable\Repository::class);
        $entity = $this->createEntity($entityName);
        $repository->setEntity($entity);

        return $repository;
    }

    /**
     * Returns a new Entity instance
     *
     * @param null|string $entityName
     * @return null|Entity
     */
    public function createEntity($entityName = null) {
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
        /**
         * @var \SplFileInfo $file
         */
        foreach ($iteratorIterator as $path => $file) {
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
     * @param $index
     * @return null|string
     */
    public function getResourceMapping($index) {
        if (isset($this->resourceMap[$index])) {
            return $this->resourceMap[$index];
        }
        return null;
    }

}