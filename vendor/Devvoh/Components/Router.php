<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Router
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Router {
    use \Devvoh\Components\Traits\GetClassName;

    protected $currentPath  = null;
    protected $routes       = null;

    /**
     * Route the path given or simply the current path being looked at
     *
     * @param null|string $path
     *
     * @return null|array|bool
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
        foreach ($this->routes as $data) {
            // Reset local $params array so we don't keep old param attempts
            $params = [];

            // Check if the request method matches the allowed methods
            if (!in_array($_SERVER['REQUEST_METHOD'], explode('|', $data['method']))) {
                // Skip this route, since the request method is invalid
                continue;
            }

            // Try simple routing first
            if ($data['path'] == $this->currentPath) {
                // All's good, it's a direct match
                return $data;
            } elseif (strpos($data['path'], ':') !== false) {
                // It's not a direct match but it does have params, so let's separate those now
                $pathParts = explode('/', ltrim($data['path'], '/'));

                // Run through the parts to see which one is actually a param
                foreach ($pathParts as $key => $part) {
                    // Check if the current part is a param
                    if (strpos($part, ':') !== false) {
                        // And store it for later
                        $params[$key] = [
                            'name' => str_replace(':', '', $part),
                        ];
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

                        // Add value to the reference
                        $params[$key]['value'] = $value;
                    } else {
                        // Not a parameter, so all we need is a matching part with the currentPathParts
                        if ($param !== $currentPathParts[$key]) {
                            // Not the same, so skip it
                            continue 2;
                        }
                    }
                }

                // If we get here, the route is a match and all the params have been added to App::getParam()
                return $data + [
                    'params' => $params,
                ];
            }
        }
        return false;
    }

    /**
     * Returns a route by name
     *
     * @param $name
     *
     * @return null|array
     */
    public function getRouteByName($name) {
        foreach ($this->routes as $routeName => $data) {
            if ($name === $routeName) {
                return $data;
            }
        }
        return null;
    }

    /**
     * Builds a path by routeName and params
     *
     * @param       $routeName
     * @param array $params
     *
     * @return string|null
     */
    public function buildRoute($routeName, $params = []) {
        // Get the route first, and if not found, return null
        $route = $this->getRouteByName($routeName);
        if (!$route) {
            return null;
        }

        // Get the path so we can mess with it if need be
        $path = $route['path'];

        // Check if we need to replace any params
        if (strpos($path, ':') !== false) {
            // Get the path and attempt to replace all param keys with its values
            foreach ($params as $key => $value) {
                $path = str_replace(':' . $key, $value, $path);
            }
        }
        return $path;
    }

    /**
     * Return the routes
     *
     * @return null|array
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Add an array of routes to the router
     *
     * @param null $routes
     *
     * @return \Devvoh\Components\Router|false
     */
    public function addRoutes($routes = null) {
        if (!$routes) {
            return false;
        }
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }
        return $this;
    }

    /**
     * Add a single route to the router
     *
     * @param $name
     * @param $route
     *
     * @return \Devvoh\Components\Router
     * @throws Exception
     */
    public function addRoute($name, $route) {
        if (isset($this->routes[$name])) {
            throw new Exception('Route already added with name: ' . $name . ' in a different module. Please use unique names.');
        }
        $this->routes[$name] = $route;
        return $this;
    }

}