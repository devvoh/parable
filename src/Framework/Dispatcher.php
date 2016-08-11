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

    /** @var \Parable\Http\Response */
    protected $response;

    /**
     * @param \Parable\Events\Hook     $hook
     * @param \Parable\Filesystem\Path $path
     * @param \Parable\Framework\View  $view
     * @param \Parable\Http\Response   $response
     */
    public function __construct(
        \Parable\Events\Hook     $hook,
        \Parable\Filesystem\Path $path,
        \Parable\Framework\View  $view,
        \Parable\Http\Response   $response
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
    public function dispatch(\Parable\Routing\Route $route) {
        $this->hook->trigger('parable_dispatch_before', $route);
        $controller = null;

        /* Start output buffering and set $content to null */
        $content = null;
        $this->response->startOutputBuffer();

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

        /* Get the output buffer content and check if $content holds anything. If so, append it to the $bufferContent */
        $bufferContent = $this->response->returnOutputBuffer();
        if ($content) {
            $bufferContent .= $content;
        }

        /* And append the content to the response object */
        $this->response->appendContent($bufferContent);

        $this->hook->trigger('parable_dispatch_after', $route);
        return $this;
    }

}
