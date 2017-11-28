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
     * Dispatch the provided route.
     *
     * @param \Parable\Routing\Route $route
     *
     * @return $this
     */
    public function dispatch(\Parable\Routing\Route $route)
    {
        $this->hook->trigger(self::HOOK_DISPATCH_BEFORE, $route);
        $controller = null;

        // Start output buffering and set $content to null
        $content = null;
        $this->response->startOutputBuffer();

        // Build the parameters array
        $parameters = [];
        foreach ($route->getValues() as $value) {
            $parameters[] = $value;
        }

        // Call the relevant code
        if ($route->hasControllerAndAction()) {
            $controller = \Parable\DI\Container::get($route->getController());
            $content = $controller->{$route->getAction()}(...$parameters);
        } elseif ($route->hasCallable()) {
            $callable = $route->getCallable();
            $content = $callable(...$parameters);
        }

        // If the route has no template path, attempt to build one based on controller/action.phtml
        if (!$route->hasTemplatePath() && $route->hasControllerAndAction()) {
            $reflection = new \ReflectionClass($controller);
            $controllerName = str_replace('\\', '/', $reflection->getName());
            $controllerName = str_replace('Controller/', '', $controllerName);

            $templatePathGenerated = $this->path->getDir(
                "app/View/{$controllerName}/{$route->getAction()}.phtml"
            );

            if (file_exists($templatePathGenerated)) {
                $route->setTemplatePath($templatePathGenerated);
            }
        }

        // And check again, now that we might have a magic template path
        if ($route->hasTemplatePath()) {
            $templatePath = $this->path->getDir($route->getTemplatePath());
            $this->view->setTemplatePath($templatePath);
            $this->view->render();
        }

        // Get the output buffer content and check if $content holds anything. If so, append it to the $bufferContent
        $content = $this->response->returnOutputBuffer() . $content;

        // And append the content to the response object
        $this->response->appendContent($content);

        $this->hook->trigger(self::HOOK_DISPATCH_AFTER, $route);
        return $this;
    }
}
