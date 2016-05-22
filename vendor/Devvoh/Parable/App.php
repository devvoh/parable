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
    protected $version           = '0.5.0';

    /** @var null|string */
    protected $baseDir           = null;

    /** @var null|string */
    protected $publicUrl         = null;

    /** @var bool */
    protected $debugEnabled      = false;

    /** @var null|array */
    protected $route             = null;

    /** @var null|string */
    protected $currentModule     = null;

    /** @var array */
    protected $modules           = [];

    /** @var \Devvoh\Components\Date */
    protected $date;

    /** @var \Devvoh\Components\Database */
    protected $database;

    /** @var \Devvoh\Components\Debug */
    protected $debug;

    /** @var \Devvoh\Components\Hook */
    protected $hook;

    /** @var \Devvoh\Components\Log */
    protected $log;

    /** @var \Devvoh\Components\Response */
    protected $response;

    /** @var \Devvoh\Components\Rights */
    protected $rights;

    /** @var \Devvoh\Components\Router */
    protected $router;

    /** @var \Devvoh\Parable\Config */
    protected $config;

    /** @var \Devvoh\Parable\Init */
    protected $init;

    /** @var \Devvoh\Parable\Session */
    protected $session;

    /** @var \Devvoh\Parable\Tool */
    protected $tool;

    /** @var \Devvoh\Parable\View */
    protected $view;

    /**
     * @param \Devvoh\Components\Date     $date
     * @param \Devvoh\Components\Database $database
     * @param \Devvoh\Components\Debug    $debug
     * @param \Devvoh\Components\Hook     $hook
     * @param \Devvoh\Components\Log      $log
     * @param \Devvoh\Components\Response $response
     * @param \Devvoh\Components\Rights   $rights
     * @param \Devvoh\Components\Router   $router
     * @param \Devvoh\Parable\Config      $config
     * @param \Devvoh\Parable\Init        $init
     * @param \Devvoh\Parable\Session     $session
     * @param \Devvoh\Parable\Tool        $tool
     * @param \Devvoh\Parable\View        $view
     */
    public function __construct(
        \Devvoh\Components\Date     $date,
        \Devvoh\Components\Database $database,
        \Devvoh\Components\Debug    $debug,
        \Devvoh\Components\Hook     $hook,
        \Devvoh\Components\Log      $log,
        \Devvoh\Components\Response $response,
        \Devvoh\Components\Rights   $rights,
        \Devvoh\Components\Router   $router,
        \Devvoh\Parable\Config      $config,
        \Devvoh\Parable\Init        $init,
        \Devvoh\Parable\Session     $session,
        \Devvoh\Parable\Tool        $tool,
        \Devvoh\Parable\View        $view
    ) {
        $this->date     = $date;
        $this->database = $database;
        $this->debug    = $debug;
        $this->hook     = $hook;
        $this->log      = $log;
        $this->response = $response;
        $this->rights   = $rights;
        $this->router   = $router;
        $this->config   = $config;
        $this->init     = $init;
        $this->session  = $session;
        $this->tool     = $tool;
        $this->view     = $view;
    }

    /**
     * Starts the App class and does some initial setup
     *
     * @return $this
     * @throws \Exception
     */
    public function boot()
    {
        $this->response->startOB();

        // Find out what modules we have
        $this->loadModules();

        $this->tool->loadResourceMap();

        // Collect the routes, now that we know our modules
        $this->collectRoutes();

        // And load the App config
        $this->config->load();

        // Set debug enabled/disabled based on config
        if ($this->config->get('debug_enabled')) {
            $this->tool->enableDebug();
            $this->debug->startTimer();
        } else {
            $this->tool->disableDebug();
        }

        // Set and enable database based on config
        if ($this->config->get('storage_type') && $this->config->get('storage_location')) {
            $location = $this->config->get('storage_location');
            if (strpos($this->config->get('storage_type'), 'sqlite') !== false) {
                $location = $this->tool->getDir($this->config->get('storage_location'));
            }
            $config = [
                'type'      => $this->config->get('storage_type'),
                'location'  => $location,
                'username'  => $this->config->get('storage_username'),
                'password'  => $this->config->get('storage_password'),
                'database'  => $this->config->get('storage_database'),
            ];
            $this->database->setConfig($config);
        }

        // Set timezone if given, otherwise default to Europe/London
        $this->date->setTimezone('Europe/London');
        if ($this->config->get('default_timezone')) {
            $this->date->setTimezone(
                $this->config->get('default_timezone')
            );
        }

        // Set the appropriate log directory & default file name
        $this->log->setPath($this->tool->getBaseDir() . 'var' . DS . 'log');

        // And see if there's additional rights levels we should add
        if ($this->config->get('rights_add')) {
            $toAdd = explode(',', $this->config->get('rights_add'));
            foreach ($toAdd as $right) {
                $this->rights->addRight(trim($right));
            }
        }

        // Start the session
        $this->session->startSession();

        // Load all module Init scripts
        $this->init->run();

        return $this;
    }

    /**
     * Dispatch the current route
     */
    public function dispatch() {
        $this->hook->trigger('parable_app_dispatch_before');
        // Try to match the path to an existing route. If no path given to ->route(), current $_GET value is used.
        $matchedRoute = $this->matchRoute();
        $dispatched = false;
        if ($matchedRoute) {
            // Create the dispatcher and try to dispatch
            $dispatcher = \Devvoh\Components\DI::get(\Devvoh\Parable\Dispatcher::class)->setRoute($matchedRoute);
            $dispatched = $dispatcher->dispatch();
        }

        if (!$dispatched) {
            $template = $this->view->partial('Error/404.phtml');
            if (!$template) {
                $template = '404: Page Not Found';
            }
            $this->response->setContent($template);
        }

        // Last thing we do is ask our Response to send it all as configured
        $this->hook->trigger('parable_app_response_sendResponse_before');
        $this->response->sendResponse();
        $this->hook->trigger('parable_app_response_sendResponse_after');
        $this->hook->trigger('parable_app_dispatch_after');
    }

    /**
     * Load the modules and store their names in $this->modules
     */
    public function loadModules() {
        $this->hook->trigger('parable_app_loadModules_before');
        $dirIt = new \RecursiveDirectoryIterator(
            $this->tool->getDir('app/modules'),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        foreach ($dirIt as $file) {
            $this->tool->addModule($file->getFileName(), [
                'name' => $file->getFileName(),
                'path' => $file->getPathName(),
            ]);
        }
        $this->hook->trigger('parable_app_loadModules_after');
    }

    /**
     * Match the route using the router and store the result with $this->setRoute()
     *
     * @return null|array
     */
    public function matchRoute() {
        $this->hook->trigger('parable_app_router_match_before');
        $this->tool->setRoute(
            $this->router->match()
        );
        $this->hook->trigger('parable_app_router_match_after');
        return $this->tool->getRoute();
    }

    /**
     * Collect routes and include the files, which will add their routes to the router automatically
     */
    public function collectRoutes() {
        foreach ($this->tool->getModules() as $module) {
            $className = $module['name'] . '\\Routes';
            if (class_exists($className)) {
                \Devvoh\Components\DI::create($className);
            }
        }
    }

}
