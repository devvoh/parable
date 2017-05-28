<?php

namespace Parable\Routing;

class Router
{
    /** @var \Parable\Http\Request */
    protected $request;

    /** @var \Parable\Http\Url */
    protected $url;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Routing\Route[] */
    protected $routes = [];

    public function __construct(
        \Parable\Http\Request $request,
        \Parable\Http\Url $url,
        \Parable\Filesystem\Path $path
    ) {
        $this->request = $request;
        $this->url     = $url;
        $this->path    = $path;
    }

    /**
     * Add a route to the routes list.
     *
     * @param string $name
     * @param array $routeArray
     *
     * @return $this
     */
    public function addRoute($name, array $routeArray)
    {
        $route = new Route($this->request, $routeArray);
        $this->routes[$name] = $route;
        return $this;
    }

    /**
     * Return a route by its name.
     *
     * @param string $name
     *
     * @return \Parable\Routing\Route
     * @throws \Parable\Routing\Exception
     */
    public function getRouteByName($name)
    {
        if (!isset($this->routes[$name])) {
            return null;
        }
        return $this->routes[$name];
    }

    /**
     * Take the current url and try to match it.
     *
     * @return \Parable\Routing\Route|null
     */
    public function matchCurrentRoute()
    {
        return $this->matchRoute($this->url->getCurrentUrl());
    }

    /**
     * Try to find a match in all available routes.
     *
     * @param string $url
     *
     * @return \Parable\Routing\Route|null
     */
    public function matchRoute($url)
    {
        $url = '/' . ltrim($url, '/');
        if ($route = $this->matchRouteDirectly($url)) {
            return $route;
        }
        if ($route = $this->matchRouteWithParameters($url)) {
            return $route;
        }
        return null;
    }

    /**
     * Loop through routes and try to match directly.
     *
     * @param string $url
     *
     * @return \Parable\Routing\Route|null
     */
    protected function matchRouteDirectly($url)
    {
        foreach ($this->routes as $route) {
            if ($route->matchDirectly($url)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Loop through routes and try to match with parameters.
     *
     * @param string $url
     *
     * @return \Parable\Routing\Route|null
     */
    protected function matchRouteWithParameters($url)
    {
        foreach ($this->routes as $route) {
            if ($route->matchWithParameters($url)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    public function getRouteUrlByName($name, array $parameters = [])
    {
        $route = $this->getRouteByName($name);
        if (!$route) {
            return null;
        }
        return $route->buildUrlWithParameters($parameters);
    }
}
