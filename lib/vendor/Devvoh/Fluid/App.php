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

    /**
     * @var null
     */
    static $baseDir = null;

    /**
     * @var null
     */
    static $publicUrl = null;

    /**
     * @var null
     */
    static $debug = null;

    /**
     * @var null
     */
    static $cli = null;

    /**
     * @var null
     */
    static $config = null;

    /**
     * @var null
     */
    static $hook = null;

    /**
     * @var null
     */
    static $log = null;

    /**
     * @var null
     */
    static $param = null;

    /**
     * @var null
     */
    static $post = null;

    /**
     * @var null
     */
    static $get = null;

    /**
     * @var null
     */
    static $messages = null;

    /**
     * @var null
     */
    static $session = null;

    /**
     * @var null
     */
    static $router = null;

    /**
     * @var null
     */
    static $routes = array();

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
        self::getSession()->start();
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
            self::$cli = new \Devvoh\Fluid\Cli();
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
            self::$hook = new \Devvoh\Fluid\App\Hook();
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
            self::$session = new \Devvoh\Fluid\App\Session();
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
            self::$param = new \Devvoh\Fluid\App\Param();
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
            self::$post = new \Devvoh\Fluid\App\Post();
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
            self::$get = new \Devvoh\Fluid\App\Get();
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
            self::$router = new \Devvoh\Fluid\App\Router();
            self::$router->collectRoutes();
        }
        return self::$router;
    }

}
