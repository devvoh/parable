<?php

namespace Parable\Framework;

class Dispatcher
{
    const HOOK_DISPATCH_BEFORE = "parable_dispatch_before";
    const HOOK_DISPATCH_AFTER  = "parable_dispatch_after";

    /** @var \Parable\Event\Hook */
    protected $hook;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Framework\View */
    protected $view;

    /** @var \Parable\Http\Response */
    protected $response;

    public function __construct(
        \Parable\Event\Hook $hook,
        \Parable\Filesystem\Path $path,
        \Parable\Framework\View $view,
        \Parable\Http\Response $response
    ) {
        $this->hook     = $hook;
        $this->path     = $path;
        $this->view     = $view;
        $this->response = $response;
    }

    /**
     * @param \Parable\Routing\Route $route
     *
     * @return $this
     */
    public function dispatch(\Parable\Routing\Route $route)
    {
        $this->hook->trigger(self::HOOK_DISPATCH_BEFORE, $route);
        $controller = null;

        /* Start output buffering and set $content to null */
        $content = null;
        $this->response->startOutputBuffer();

        /* Build the parameters array */
        $parameters = [$route];
        foreach ($route->getValues() as $value) {
            $parameters[] = $value;
        }

        /* Call the relevant code */
        if ($route->controller && $route->action) {
            $controller = \Parable\DI\Container::get($route->controller);
            $content = $controller->{$route->action}(...$parameters);
        } elseif ($route->callable) {
            $call = $route->callable;
            $content = $call(...$parameters);
        }

        /* Try to get the relevant view */
        $templateFile = null;
        if ($route->template) {
            $templateFile = $this->path->getDir($route->template);
        } else {
            if ($controller) {
                $reflection = new \ReflectionClass($controller);
                $controllerName = str_replace('\\', '/', $reflection->getName());
                $controllerName = str_replace('Controller/', '', $controllerName);
                $templateFile = $this->path->getDir(
                    "app/View/{$controllerName}/{$route->action}.phtml"
                );
            }
        }

        if ($templateFile && file_exists($templateFile)) {
            $this->view->setTemplatePath($templateFile);
            $this->view->render();
        }

        /* Get the output buffer content and check if $content holds anything. If so, append it to the $bufferContent */
        $content = $this->response->returnOutputBuffer() . $content;

        /* And append the content to the response object */
        $this->response->appendContent($content);

        $this->hook->trigger(self::HOOK_DISPATCH_AFTER, $route);
        return $this;
    }
}
