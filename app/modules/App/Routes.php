<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App;

class Routes {

    /** @var \Devvoh\Parable\Tool */
    protected $tool;

    /** @var \Devvoh\Components\Router */
    protected $router;

    /**
     * @param \Devvoh\Parable\Tool      $tool
     * @param \Devvoh\Components\Router $router
     */
    public function __construct(
        \Devvoh\Parable\Tool      $tool,
        \Devvoh\Components\Router $router
    ) {
        $this->tool   = $tool;
        $this->router = $router;

        $this->registerRoutes();
    }

    /**
     *
     */
    protected function registerRoutes() {
        $routes = [
            'index' => [
                'method' => 'GET',
                'path' => '/',
                'controller' => 'Home',
                'action' => 'index',
            ],
            'closure' => [
                'method' => 'GET',
                'path' => '/closure',
                'closure' => function() {
                    return 'this is a closure';
                },
            ],
        ];

        // Add module to all routes
        foreach ($routes as &$route) {
            $route['module'] = $this->tool->getModuleFromPath(__DIR__);
        }
        $this->router->addRoutes($routes);
    }

}

