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
            $params = array();

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
                foreach ($params as $key => &$param) {
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
                                    // Not good, go to the next route (continue from switch/foreach/foreach)
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
                                    // Not good, go to the next route (continue from switch/foreach/foreach)
                                    continue(3);
                                }
                                // All good, break the switch
                                break;
                        }
                        // Add value to the reference
                        $param['value'] = $value;
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
                    'params' => $params,
                );
            }
        }
        return false;
    }

    /**
     * Add an array of routes to the router
     *
     * @param null $routes
     *
     * @return $this|bool
     */
    public function addRoutes($routes = null) {
        if (!$routes) {
            return false;
        }
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
        return $this;
    }

    /**
     * Add a single route to the router
     *
     * @param $route
     *
     * @return $this
     */
    public function addRoute($route) {
        $this->routes[$route['path']] = $route;
        return $this;
    }

}