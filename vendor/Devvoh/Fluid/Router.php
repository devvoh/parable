<?php
/**
 * Fluid - Router.php
 *
 * Routes urls to closures
 *
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid;

class Router {

    protected $url = '/';
    protected $routes = null;
    protected $currentClosure = null;
    protected $currentParams = null;

    public function __construct() {
        if (isset($_GET['url'])) {
            if (substr($_GET['url'], -1) === '/') {
                $_GET['url'] = substr($_GET['url'], 0, -1);
            }
            $this->url .= $_GET['url'];
        }
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function map(array $methods, $pattern, $closure) {
        foreach ($methods as $method) {
            if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
                return false;
            }
        }
        if (empty($pattern) || !is_string($pattern)) {
            return false;
        }
        if (!is_callable($closure)) {
            return false;
        }

        foreach ($methods as $method) {
            $this->routes[$method][$pattern] = $closure;
        }
        return $this;
    }
    
    public function match() {
        if ($this->matchRoute()) {
            $closure = $this->currentClosure;
            $closure($this->currentParams);
        } else {
            echo '404: ' . $this->url;
        }
    }

    public function matchRoute() {
        $closure = null;
        $params = null;

        // Check if this is a literal match, and if so, that's our match
        if (isset($this->routes[$this->method][$this->url])) {
            $this->currentClosure = $this->routes[$this->method][$this->url];
            return true;
        } else {
            // It must be a dynamic route, right?
            foreach ($this->routes[$this->method] as $pattern => $route) {
                // Reset
                $params  = null;
                $closure = null;

                $routeParts = explode('/', $pattern);
                $urlParts   = explode('/', $this->url);
                if (count($routeParts) !== count($urlParts)) {
                    continue;
                }

                foreach ($routeParts as $key => $part) {
                    if (mb_strpos($part, '{') !== false) {
                        $partKey          = str_replace(array('{', '}'), '', $part);
                        $params[$partKey] = $urlParts[$key];
                        $urlParts[$key]   = $part;
                    }
                }

                // Check if we've got a match
                if ($routeParts === $urlParts) {
                    $this->currentClosure = $this->routes[$this->method][$pattern];
                    return $this->checkParams($params);
                }
            }
        }
        return false;
    }
    
    public function checkParams($params) {
        $this->currentParams = null;
        foreach ($params as $pattern => $value) {
            list($type, $name) = explode(':', $pattern);
            switch ($type) {
                case 'i':
                    if (!ctype_digit($value) && mb_strpos($name, '@') !== false) {
                        return false;
                    }
                    break;
                case 'a':
                    if (!ctype_alpha($value) && mb_strpos($name, '@') !== false) {
                        return false;
                    }
                    break;
            }
            $name = str_replace('@', '', $name);
            $this->currentParams[$name] = $value;
        }
        return true;
        
    }

}