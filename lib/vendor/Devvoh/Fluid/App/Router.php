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
    public function match($path = null) {
        if (!$this->routes) {
            return false;
        }

        if (!$path && isset($_GET['path'])) {
            $path = $_GET['path'];
        }
        $this->currentPath = '/' . trim($path, '/');

        // Try simple routing first
        foreach ($this->routes as $module => $routes) {
            foreach ($routes as $data) {
                // Reset local $params array so we don't keep old param attempts 
                $params = array();
                
                // Check if the request method matches the allowed methods
                if (!in_array($_SERVER['REQUEST_METHOD'], explode('|', $data['method']))) {
                    // Skip this route, since the request method is invalid
                    continue;
                }

                // Try simple routing first
                if ($data['path'] == $this->currentPath) {
                    // All's good, it's a direct match
                    return $data + array(
                        'module' => $module
                    );
                } elseif (strpos($data['path'], ':') !== false) {
                    // It's not a direct match but it does have params, so let's separate those now
                    $pathParts = explode('/', ltrim($data['path'], '/'));
                    
                    // Run through the parts to see which one is actually a param
                    foreach ($pathParts as $key => $part) {
                        // Check if the current part is a param
                        if (strpos($part, ':') !== false) {
                            // Separate the param into type & name
                            list($type, $name) = explode(':', $part);
                            
                            // And store it for later
                            $params[$key] = array(
                                'type' => $type,
                                'name' => $name,
                            );
                        } else {
                            // Not a param, so just put in the part
                            $params[$key] = $part;
                        }
                    }
                    
                    // Break up the current path in equal parts to the path parts
                    $currentPathParts = explode('/', trim($this->currentPath, '/'));
                    
                    // If the amount of parts is not the same, it can't be the same path
                    if (count($pathParts) !== count($currentPathParts)) {
                        continue;
                    }
                    
                    // Now loop through the params and match with values
                    foreach ($params as $key => $param) {
                        // $param is not an array if it's not a param
                        if (is_array($param)) {
                            $value = $currentPathParts[$key];
                            
                            // Now check for typecasting
                            switch ($param['type']) {
                                case 'i':
                                    // Check if it's an invalid type for integer
                                    if (
                                        !ctype_digit($value)
                                        || $value != (int)$value
                                    ) {
                                        continue(3);
                                    }
                                    // All good, break the switch
                                    break;
                                case 'a':
                                    // Check if it's an invalid type for alpha
                                    if (
                                        !ctype_alpha($value)
                                        || $value != (int)$value
                                    ) {
                                        continue(3);
                                    }
                                    // All good, break the switch
                                    break;
                            }
                            
                            // Since it's a valid parameter, so let's add it to Param
                            App::getParam()->setValue($param['name'], $value);
                        } else {
                            // Not a parameter, so all we need is a matching part with the currentPathParts
                            if ($param !== $currentPathParts[$key]) {
                                // Not the same, so skip it
                                continue;
                            }
                        }
                    }
                    
                    // If we get here, the route is a match and all the params have been added to App::getParam()
                    return $data + array(
                        'module' => $module
                    );
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
        // Check if there is a closure rather than a controller. If so, call it and we're done.
        if (isset($route['closure']) && is_callable($route['closure'])) {
            $route['closure']();
            return true;
        }
        
        // If not a closure, we're going to need to load a controllerFile at least
        $controllerFile = App::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'controller' . DS . $route['controller'] . '.php';
        // A view file is optional, but we may want to include it if it exists
        $viewFile = App::getBaseDir() . 'app/modules' . DS . $route['module'] . DS . 'view' . DS . $route['controller'] . DS . $route['action'] . '.phtml';
        
        // Check if the controllerfile exists
        if (!file_exists($controllerFile)) {
            return false;
        }
        
        // Since the file exists, let's require it
        require_once($controllerFile);

        // Get all the data
        $controllerName = $route['controller'];
        $action         = $route['action'];
        $controller     = new $controllerName();

        // And check if the action exists on the controller
        if (!method_exists($controller, $action)) {
            return false;
        }
        
        // It does, so call it
        $controller->$action();
        
        // Include a view file if we have one
        if (file_exists($viewFile)) {
            require_once($viewFile);
        }
        return true;
    }

}