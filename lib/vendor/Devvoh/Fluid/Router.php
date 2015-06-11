<?php
namespace Devvoh\Fluid;

class Router {

    protected $url = '/';
    protected $routes = null;

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
        $closure = null;
        $params = null;

        // Check if this is a literal match, and if so, that's our match
        if (isset($this->routes[$this->method][$this->url])) {
            $closure = $this->routes[$this->method][$this->url]();
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

                if ($routeParts === $urlParts) {
                    $closure = $this->routes[$this->method][$pattern];
                    break;
                }
            }
        }
        if (is_callable($closure)) {
            $closure($params);
            return true;
        }
        echo '404';
        return false;
    }

}