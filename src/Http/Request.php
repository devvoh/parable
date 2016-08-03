<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http;

class Request {

    /** @var string */
    protected $method;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param $method
     *
     * @return bool
     */
    public function isMethod($method) {
        return (bool)$this->method === $method;
    }

    /**
     * @return bool
     */
    public function isGet() {
        return $this->isMethod('GET');
    }

    /**
     * @return bool
     */
    public function isPost() {
        return $this->isMethod('POST');
    }

    /**
     * @return bool
     */
    public function isUpdate() {
        return $this->isMethod('UPDATE');
    }

    /**
     * @return bool
     */
    public function isDelete() {
        return $this->isMethod('DELETE');
    }

    /**
     * @return bool
     */
    public function isPatch() {
        return $this->isMethod('PATCH');
    }

}
