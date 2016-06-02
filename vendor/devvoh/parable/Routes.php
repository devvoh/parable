<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Parable;

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
    }

    /**
     * Registers the array given and adds the module to them
     *
     * @param array  $routes
     * @param string $modulePath
     *
     * @return $this
     */
    protected function registerRoutes(array $routes, $modulePath) {
        // Add module to all routes if we've been given a path
        if ($modulePath) {
            foreach ($routes as &$route) {
                $route['module'] = $this->tool->getModuleFromPath($modulePath);
            }
        }
        $this->router->addRoutes($routes);
        return $this;
    }

}
