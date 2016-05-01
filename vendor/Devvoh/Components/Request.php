<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Request {

    protected $method       = null;
    protected $userAgent    = null;
    protected $scheme       = null;

    public function __construct() {
        $this->setMethod($_SERVER['REQUEST_METHOD']);
        $this->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $this->setScheme($_SERVER['REQUEST_SCHEME']);
    }

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }

    public function isMethod($method) {
        if ($this->method === strtoupper($method)) {
            return true;
        }
        return false;
    }

    public function isPost() {
        return $this->isMethod('POST');
    }

    public function isGet() {
        return $this->isMethod('GET');
    }

    public function isPut() {
        return $this->isMethod('PUT');
    }

    public function isDelete() {
        return $this->isMethod('DELETE');
    }

    public function isPatch() {
        return $this->isMethod('PATCH');
    }

    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getUserAgent() {
        return $this->userAgent;
    }

    public function setScheme($scheme) {
        $this->scheme = $scheme;
        return $this;
    }

    public function getScheme() {
        return $this->scheme;
    }

}