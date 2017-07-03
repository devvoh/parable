<?php

namespace Parable\Framework;

class App
{
    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Framework\Config */
    protected $config;

    /** @var \Parable\Framework\Dispatcher */
    protected $dispatcher;

    /** @var \Parable\Framework\Toolkit */
    protected $toolkit;

    /** @var \Parable\Event\Hook */
    protected $hook;

    /** @var \Parable\Routing\Router */
    protected $router;

    /** @var \Parable\Http\Response */
    protected $response;

    /** @var \Parable\Http\Url */
    protected $url;

    /** @var \Parable\GetSet\Session */
    protected $session;

    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var string */
    protected $version = '0.11.4';

    public function __construct(
        \Parable\Filesystem\Path $path,
        \Parable\Framework\Config $config,
        \Parable\Framework\Dispatcher $dispatcher,
        \Parable\Framework\Toolkit $toolkit,
        \Parable\Event\Hook $hook,
        \Parable\Routing\Router $router,
        \Parable\Http\Response $response,
        \Parable\Http\Url $url,
        \Parable\GetSet\Session $session,
        \Parable\ORM\Database $database
    ) {
        $this->path       = $path;
        $this->config     = $config;
        $this->dispatcher = $dispatcher;
        $this->toolkit    = $toolkit;
        $this->hook       = $hook;
        $this->router     = $router;
        $this->response   = $response;
        $this->url        = $url;
        $this->session    = $session;
        $this->database   = $database;
    }

    /**
     * Do all the setup
     *
     * @return $this
     */
    public function run()
    {
        /* Load all known Config files now that we know the baseDir */
        $this->hook->trigger('parable_config_load_before');
        $this->config->load();
        $this->hook->trigger('parable_config_load_after', $this->config);

        /* Start the session if session.autoEnable is true */
        if ($this->config->get('session.autoEnable') !== false) {
            $this->hook->trigger('parable_session_start_before');
            $this->session->start();
            $this->hook->trigger('parable_session_start_after', $this->session);
        }

        /* Build the base Url */
        $this->url->buildBaseurl();

        /* Load the routes */
        $this->loadRoutes();

        /* Get the current url */
        $currentUrl     = $this->toolkit->getCurrentUrl();
        $currentFullUrl = $this->toolkit->getCurrentUrlFull();

        /* Load the config */
        if ($this->config->get('database.type')) {
            $this->database->setConfig($this->config->get('database'));
        }

        /* See if there's an init directory defined in the config */
        if ($this->config->get('initLocations')) {
            $this->loadInits();
        }

        /* And try to match the route */
        $this->hook->trigger('parable_route_match_before', $currentUrl);
        $route = $this->router->matchUrl($this->toolkit->getCurrentUrl());
        $this->hook->trigger('parable_route_match_after', $route);
        if ($route) {
            $this->response->setHttpCode(200);
            $this->hook->trigger('parable_http_200', $route);
            $this->dispatcher->dispatch($route);
        } else {
            $this->response->setHttpCode(404);
            $this->hook->trigger('parable_http_404', $currentFullUrl);
        }

        $this->hook->trigger('parable_response_send');
        $this->response->send();
        return $this;
    }

    /**
     * Load the routes from \Routing\App
     *
     * @return $this
     */
    protected function loadRoutes()
    {
        try {
            $routing = \Parable\DI\Container::get(\Routing\App::class);

            foreach ($routing->get() as $name => $route) {
                $this->router->addRoute($name, $route);
            }
        } catch (\Parable\DI\Exception $e) {
            // Let it slip through
        }

        return $this;
    }

    /**
     * Create instances of available init files in initLocations.
     *
     * @return $this
     *
     * @throws \Parable\Framework\Exception
     */
    protected function loadInits()
    {
        $locations = $this->config->get('initLocations');

        foreach ($locations as $location) {
            $directory = $this->path->getDir($location);

            if (!file_exists($directory)) {
                throw new \Parable\Framework\Exception("initLocation does not exist: '{$directory}");
            }

            $dirIterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
            $iteratorIterator = new \RecursiveIteratorIterator($dirIterator);

            foreach ($iteratorIterator as $file) {
                /** @var \SplFileInfo $file */
                if ($file->getExtension() !== 'php') {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }

                $className = '\\Init\\' . str_replace('.' . $file->getExtension(), '', $file->getFilename());
                \Parable\DI\Container::create($className);
            }
        }
        return $this;
    }

    /**
     * Return the version number
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
