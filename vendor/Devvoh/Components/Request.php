<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Request {

    /** @var null|string */
    protected $method;

    /** @var null|string */
    protected $userAgent;

    /** @var null|string */
    protected $scheme;

    /**
     * Set the method, userAgent & scheme upon load
     */
    public function __construct() {
        $this->method    = $_SERVER['REQUEST_METHOD'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->scheme    = $_SERVER['REQUEST_SCHEME'];
    }

    /**
     * Return the method used
     *
     * @return null|string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Check whether the request method is equal to $method
     *
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method) {
        if ($this->method === strtoupper($method)) {
            return true;
        }
        return false;
    }

    /**
     * Return whether the method is POST
     *
     * @return bool
     */
    public function isPost() {
        return $this->isMethod('POST');
    }

    /**
     * Return whether the method is GET
     *
     * @return bool
     */
    public function isGet() {
        return $this->isMethod('GET');
    }

    /**
     * Return whether the method is PUT
     *
     * @return bool
     */
    public function isPut() {
        return $this->isMethod('PUT');
    }

    /**
     * Return whether the method is DELETE
     *
     * @return bool
     */
    public function isDelete() {
        return $this->isMethod('DELETE');
    }

    /**
     * Return whether the method is PATCH
     *
     * @return bool
     */
    public function isPatch() {
        return $this->isMethod('PATCH');
    }

    /**
     * Return the user agent
     *
     * @return null|string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Return the HTTP/S scheme
     *
     * @return null|string
     */
    public function getScheme() {
        return $this->scheme;
    }

}
