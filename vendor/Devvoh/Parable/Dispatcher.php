<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

use \Devvoh\Parable\App;

class Dispatcher {

    /** @var null|array */
    protected $route = null;

    /**
     * Dispatcher constructor.
     *
     * @param array $route
     */
    public function __construct(array $route = []) {
        $this->setRoute($route);
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
        // Store the current module on App
        App::setCurrentModule($this->getRoute()['module']);

        // Get the route
        $route = $this->getRoute();

        // Trigger the parable_dispatcher_execute_before execute
        App::getHook()->trigger('parable_dispatcher_execute_before', $route);

        // Execute the route
        $return = $this->execute();

        // Trigger the parable_dispatcher_execute_after execute
        App::getHook()->trigger('parable_dispatcher_execute_after', $route);

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
        App::getResponse()->startOB();

        // Check for route params and set them as App params
        if (isset($route['params'])) {
            foreach ($route['params'] as $param) {
                if (!is_array($param)) {
                    continue;
                }
                App::getParam()->set($param['name'], $param['value']);
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
            $controllerName = '\\' . $route['module'] . '\\' . 'Controller' . '\\' . $route['controller'];
            $action = $route['action'];

            // And start an instance
            $controller = new $controllerName();

            // Check whether the action exists, return false if not
            if (!method_exists($controller, $route['action'])) {
                return false;
            }

            // Call the action on the controller and store the return value in $content
            $content = $controller->$action();
        }

        // If $content is set, add it to the Response
        if ($content) {
            App::getResponse()->appendContent($content);
        }

        // If $template is set, load the template on the View
        if ($template) {
            App::getView()->loadTemplate($template);
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
            $template = App::getDir(
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
            $template = App::getDir(
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
