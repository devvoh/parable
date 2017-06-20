<?php

namespace Parable\Framework;

class Toolkit
{
    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Http\Response */
    protected $response;

    /** @var \Parable\Http\Url */
    protected $url;

    /** @var \Parable\Routing\Router */
    protected $router;

    public function __construct(
        \Parable\Filesystem\Path $path,
        \Parable\Http\Response $response,
        \Parable\Http\Url $url,
        \Parable\Routing\Router $router
    ) {
        $this->path     = $path;
        $this->response = $response;
        $this->url      = $url;
        $this->router   = $router;
    }

    /**
     * Create a repository to work with model of type $modelName (full namespaced name)
     *
     * @param string $modelName
     *
     * @return \Parable\ORM\Repository
     */
    public function getRepository($modelName)
    {
        /** @var \Parable\ORM\Model $model */
        $model = \Parable\DI\Container::create($modelName);

        /** @var \Parable\ORM\Repository $repository */
        $repository = \Parable\DI\Container::create(\Parable\ORM\Repository::class);

        $repository->setModel($model);
        return $repository;
    }

    /**
     * Redirect directly by using a route name.
     *
     * @param string $routeName
     * @throws \Parable\Framework\Exception
     */
    public function redirectToRoute($routeName)
    {
        $route = $this->router->getRouteByName($routeName);
        if (!$route) {
            throw new \Parable\Framework\Exception("Can't redirect to route, '{$routeName}'' does not exist.");
        }
        $this->response->redirect($route->url);
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    public function getFullRouteUrlByName($name, array $parameters = [])
    {
        return $this->url->getUrl($this->router->getRouteUrlByName($name, $parameters));
    }
}
