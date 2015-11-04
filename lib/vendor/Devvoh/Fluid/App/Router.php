<?php
/**
 * @package     Fluid
 * @subpackage  Router
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

use Devvoh\Fluid\App as App;

class Router {

    /**
     * @var null
     */
    protected $currentPath = null;

    /**
     * @var null
     */
    public $routes = null;

    /**
     * Route the path given or simply the current path being looked at
     *
     * @param null|string $path
     *
     * @return null
     */
    public function route($path = null) {
        if (!$this->routes) {
            return false;
        }

        if (!$path && isset($_GET['path'])) {
            $path = $_GET['path'];
        }
        $this->currentPath = '/' . ltrim($path, '/');

        // route now
        foreach ($this->routes as $module => $routes) {
            foreach ($routes as $name => $data) {
                if ($data['path'] == $this->currentPath) {
                    // Check if the request method matches the allowed methods
                    if (in_array($_SERVER['REQUEST_METHOD'], explode('|', $data['method']))) {
                        return $data + array(
                            'module' => $module,
                            'name'   => $name,
                        );
                    } else {
                        return false;
                    };
                }
            }
        }

        return false;
    }

    public function addRoutes($module = null, $routes = null) {
        if (!$module || !$routes) {
            return false;
        }

        foreach ($routes as $path => $route) {
            $this->addRoute($module, $path, $route);
        }
    }

    public function addRoute($module, $path, $route) {
        if (!isset($this->routes[$module])) {
            $this->routes[$module] = array();
        }
        $this->routes[$module][$path] = $route;
    }

    public static function collectRoutes() {
        $dir = App::getDir('app/modules') . DS . '*';
        foreach (glob($dir) as $filename) {
            $routerFilename = $filename . DS . 'routes' . DS . 'routes.php';
            if (file_exists($routerFilename)) {
                require_once($routerFilename);
            }
        }
    }

    public static function execute($route) {
        $controllerFile = App::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'controller' . DS . $route['controller'] . '.php';
        $viewFile = App::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'view' . DS . $route['controller'] . DS . $route['action'] . '.phtml';
        if (file_exists($controllerFile)) {
            require_once($controllerFile);

            // Get all the data
            $controllerName = $route['controller'];
            $action         = $route['action'];
            $controller     = new $controllerName();

            // And call the action if it exists
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                return false;
            }
        }
        if (file_exists($viewFile)) {
            require_once($viewFile);
        }
        return false;
    }

}