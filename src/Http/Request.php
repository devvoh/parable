<?php

namespace Parable\Http;

class Request
{
    /** @var string */
    protected $protocol;

    /** @var string */
    protected $method;

    /** @var array */
    protected $headers = [];

    /**
     * Set some basic information we're going to need.
     */
    public function __construct()
    {
        if (PHP_SAPI !== "cli") {
            // @codeCoverageIgnoreStart
            $this->headers = getallheaders() ?: [];
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : "HTTP/1.1";
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() === $method;
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * @return bool
     */
    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * @return bool
     */
    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    public function getHeader($key)
    {
        if (!isset($this->headers[$key])) {
            return null;
        }
        return $this->headers[$key];
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * This is surprisingly annoying due to unreliable
     * availability of $_SERVER values.
     *
     * @return string
     */
    public function getScheme()
    {
        if (isset($_SERVER["REQUEST_SCHEME"])) {
            // Apache 2.4+
            return $_SERVER["REQUEST_SCHEME"];
        }
        if (isset($_SERVER["REDIRECT_REQUEST_SCHEME"])) {
            return $_SERVER["REDIRECT_REQUEST_SCHEME"];
        }
        if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"])) {
            // Sometimes available in proxied requests
            return $_SERVER["HTTP_X_FORWARDED_PROTO"];
        }
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") {
            // Old-style but compatible with IIS
            return "https";
        }
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
            // Hacky but this is our last attempt, so why not
            return "https";
        }
        return "http";
    }
}
