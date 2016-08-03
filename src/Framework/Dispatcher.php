<?php
/**
 * @package     Parable Framework
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Framework;

class Dispatcher {

    /** @var \Parable\Events\Hook */
    protected $hook;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Framework\View */
    protected $view;

    /**
     * @param \Parable\Events\Hook     $hook
     * @param \Parable\Filesystem\Path $path
     * @param \Parable\Framework\View  $view
     */
    public function __construct(
        \Parable\Events\Hook     $hook,
        \Parable\Filesystem\Path $path,
        \Parable\Framework\View  $view
    ) {
        $this->hook = $hook;
        $this->path = $path;
        $this->view = $view;
    }

    /**
     * @param \Parable\Routing\Route $route
     *
     * @return string
     */
    public function dispatch(\Parable\Routing\Route $route) {
        $this->hook->trigger('parable_dispatch_before', $route);
        $content    = '';
        $controller = null;

        /* Call the relevant code */
        if ($route->controller && $route->action) {
            $controller = \Parable\DI\Container::get($route->controller);
            $content = $controller->{$route->action}($route);
        } elseif ($route->callable) {
            $call = $route->callable;
            $content = $call($route);
        }

        /* Try to get the relevant view */
        $templateFile = null;
        if ($route->template) {
            $templateFile = $this->path->getDir($templateFile);
        } else {
            if ($controller) {
                $reflection = new \ReflectionClass($controller);

                $templateFile = $this->path->getDir('app/View/' . $reflection->getShortName() . '/' . $route->action . '.phtml');
            }
        }

        if ($templateFile && file_exists($templateFile)) {
            $this->view->setTemplatePath($templateFile);
            $this->view->render();
        }

        $this->hook->trigger('parable_dispatch_after', $route);
        return $content ?: '';
    }

}
