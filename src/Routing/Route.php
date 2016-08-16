<?php
/**
 * @package     Parable Routing
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Routing;

class Route {

    /** @var array */
    public $methods = [];

    /** @var string */
    public $name;

    /** @var string */
    public $url;

    /** @var string */
    public $controller;

    /** @var string */
    public $action;

    /** @var callable */
    public $callable;

    /** @var string */
    public $template;

    /** @var array */
    public $parameters = [];

    /** @var array */
    public $values = [];

    /** @var \Parable\Http\Request */
    protected $request;

    /**
     * Route constructor.
     *
     * @param \Parable\Http\Request $request
     * @param array                 $data
     *
     * @throws \Exception
     */
    public function __construct(
        \Parable\Http\Request $request,
        array $data
    ) {
        $this->request    = $request;

        $this->methods    = explode('|', $data['methods']);
        $this->url        = isset($data['url'])        ? $data['url']        : null;
        $this->controller = isset($data['controller']) ? $data['controller'] : null;
        $this->action     = isset($data['action'])     ? $data['action']     : null;
        $this->callable   = isset($data['callable'])   ? $data['callable']   : null;
        $this->template   = isset($data['template'])   ? $data['template']   : null;

        if (!$this->controller && !$this->action && !$this->callable) {
            throw new \Exception('Either a controller/action combination or callable is required.');
        }

        $this->parseUrlParameters();
    }

    /**
     * Parse the parameters in $this->url and store them as index => name, so we can look for values later.
     * on.
     *
     * @return $this
     */
    public function parseUrlParameters() {
        $urlParts = explode('/', $this->url);
        $this->parameters = [];
        foreach ($urlParts as $index => $part) {
            if (mb_substr($part, 0, 1) === '{' && mb_substr($part, -1) === '}') {
                $this->parameters[$index] = mb_substr($part, 1, -1);
            }
        }
        return $this;
    }

    /**
     * Take $url and get all the values and store them in $this->values.
     *
     * @param $url
     *
     * @return array
     */
    public function extractParameterValues($url) {
        $urlParts = explode('/', $url);
        $this->values = [];
        foreach ($this->parameters as $index => $name) {
            $this->values[$name] = $urlParts[$index];
        }
        return $this->values;
    }

    /**
     * Take $url and replace the 'values' with the parameters they represent. This gives us a 'corrected' url,
     * which can be directly matched with the route's url.
     *
     * @param $url
     *
     * @return string
     */
    public function injectParameters($url) {
        $urlParts = explode('/', $url);
        $parameters = array_flip($this->values);
        foreach ($urlParts as &$part) {
            if (isset($parameters[$part])) {
                $part = '{' . ltrim($parameters[$part], '{}') . '}';
            }
        }
        return implode('/', $urlParts);
    }

    /**
     * Attempt to match $url to this route's url directly.
     *
     * @param $url
     *
     * @return bool
     */
    public function matchDirectly($url) {
        if (
            !$this->isAcceptedRequestMethod()
        ) {
            return false;
        }

        if ($this->url === $url) {
            return true;
        }
        return false;
    }

    /**
     * Attempt to match $url to this route's url but also extract parameters & values.
     *
     * @param string $url
     *
     * @return bool
     */
    public function matchWithParameters($url) {
        if (
            !$this->parameters
            || !$this->isAcceptedRequestMethod()
            || !$this->isPartCountSame($url)
            || !$this->hasParameters()
        ) {
            return false;
        }

        $this->extractParameterValues($url);
        if (!$this->values) {
            return false;
        }
        $correctedUrl = $this->injectParameters($url);
        if ($this->matchDirectly($correctedUrl)) {
            return true;
        }
        return false;
    }

    /**
     * Verify whether the request method is an accepted method according to $this->methods.
     *
     * @return bool
     */
    public function isAcceptedRequestMethod() {
        return in_array($this->request->getMethod(), $this->methods);
    }

    /**
     * Verify whether this route's url has the same 'part' count.
     *
     * @param string $url
     *
     * @return bool
     */
    public function isPartCountSame($url) {
        return count(explode('/', $url)) === count(explode('/', $this->url));
    }

    /**
     * Check whether the url has parameters
     *
     * @return bool
     */
    public function hasParameters() {
        return mb_strpos($this->url, '{') && mb_strpos($this->url, '}');
    }

    /**
     * Get a value, if it exists.
     *
     * @param string $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function getValue($key) {
        if (!isset($this->values[$key])) {
            return null;
        }
        return $this->values[$key];
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    public function buildUrlWithParameters($parameters = []) {
        $url = $this->url;

        if (!$this->hasParameters() && !$parameters) {
            return $url;
        }

        foreach ($parameters as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        return $url;
    }

}
