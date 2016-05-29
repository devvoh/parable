<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Dispatcher {

    /** @var \Devvoh\Parable\App */
    protected $app;

    /** @var \Devvoh\Components\Hook */
    protected $hook;

    /** @var \Devvoh\Components\Response */
    protected $response;

    /** @var \Devvoh\Parable\Param */
    protected $param;

    /** @var \Devvoh\Parable\Tool */
    protected $tool;

    /** @var \Devvoh\Parable\View */
    protected $view;

    /** @var null|array */
    protected $route;

    /**
     * @param \Devvoh\Components\Hook     $hook
     * @param \Devvoh\Components\Response $response
     * @param \Devvoh\Parable\App         $app
     * @param \Devvoh\Parable\Param       $param
     * @param \Devvoh\Parable\Tool        $tool
     * @param \Devvoh\Parable\View        $view
     */
    public function __construct(
        \Devvoh\Components\Hook     $hook,
        \Devvoh\Components\Response $response,
        \Devvoh\Parable\App         $app,
        \Devvoh\Parable\Param       $param,
        \Devvoh\Parable\Tool        $tool,
        \Devvoh\Parable\View        $view
    ) {
        $this->app      = $app;
        $this->hook     = $hook;
        $this->response = $response;
        $this->param    = $param;
        $this->tool     = $tool;
        $this->view     = $view;
    }

    /**
     * Return the route
     *
     * @return array
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set the route
     *
     * @param array $route
     *
     * @return $this
     */
    public function setRoute(array $route) {
        $this->route = $route;
        return $this;
    }

    /**
     * Call all relevant methods in order
     *
     * @return $this
     */
    public function dispatch() {
        // Store the current module on Tool
        $this->tool->setCurrentModule($this->getRoute()['module']);

        // Get the route
        $route = $this->getRoute();

        // Trigger the parable_dispatcher_execute_before execute
        $this->hook->trigger('parable_dispatcher_execute_before', $route);

        // Execute the route
        $return = $this->execute();

        // Trigger the parable_dispatcher_execute_after execute
        $this->hook->trigger('parable_dispatcher_execute_after', $route);
        return $return;
    }

    /**
     * Execute a route
     *
     * @return bool
     */
    public function execute() {
        // Get the route
        $route = $this->getRoute();

        // Start a new level of output buffering to put whatever we're going to output into the Response
        $this->response->startOB();

        // Check for route params and set them as App params
        if (isset($route['params'])) {
            foreach ($route['params'] as $param) {
                if (!is_array($param)) {
                    continue;
                }
                $this->param->set($param['name'], $param['value']);
            }
        }

        // Get the template, if any
        $template = $this->getTemplate();

        // Set $content to null in case there's no return value for the closure/controller
        $content = null;

        // Check if we're dealing with a closure
        if (isset($route['closure'])) {
            $closure = $route['closure'];

            // If the closure isn't callable, return false
            if (!is_callable($closure)) {
                return false;
            }

            // Call the closure and store the return value in $content
            $content = $closure();
        } elseif (isset($route['controller']) && isset($route['action'])) {
            // Get the data from the route
            $controllerName = $route['module'] . '\\' . 'Controller' . '\\' . $route['controller'];
            $action = $route['action'];

            // And start an instance
            $controller = \Devvoh\Components\DI::create($controllerName);

            // Check whether the action exists, return false if not
            if (!method_exists($controller, $route['action'])) {
                return false;
            }

            // Call the action on the controller and store the return value in $content
            $content = $controller->$action();
        }

        // If $content is set, add it to the Response
        if ($content) {
            $this->response->appendContent($content);
        }

        // If $template is set, load the template on the View
        if ($template) {
            $this->view->loadTemplate($template);
        }

        // And if we've gotten this far, let's just return true
        return true;
    }

    /**
     * Return the template, if a relevant template can be found
     *
     * @return null|string
     */
    public function getTemplate() {
        $route = $this->getRoute();

        // If there is a view value in the route, use that information. If not, try to auto-gen a path.
        $template = null;
        // If a view file is given, this will take precedence over an auto-generated template
        if (isset($route['view'])) {
            $template = $this->tool->getDir(
                'app/modules' . DS . $route['module'] . DS . 'View' . DS . $route['view']
            );
        }

        // If the template doesn't exist, reset its value to null
        if ($template && !file_exists($template)) {
            $template = null;
        }

        // If template was never set or reset above, and there's a controller, we're going to see if a view file exists
        // in the default location.
        if (!$template && isset($route['controller'])) {
            $template = $this->tool->getDir(
                'app/modules' . DS . $route['module'] . DS . 'View' . DS . $route['controller'] . DS . $route['action'] . '.phtml'
            );
        }

        // If the template doesn't exist, reset its value to null
        if ($template && !file_exists($template)) {
            $template = null;
        }

        return $template;
    }
}
