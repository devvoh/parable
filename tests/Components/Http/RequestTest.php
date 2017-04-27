<?php

namespace Parable\Tests\Components\Http;

class RequestTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Request */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        // We need to set up the $_SERVER array first
        $GLOBALS['_SERVER']['REQUEST_METHOD'] = "GET";

        $this->request = new \Parable\Http\Request();
    }

    /**
     * @return string
     */
    public function testGetMethod()
    {
        die($this->request->getMethod());
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function testIsMethod($method)
    {
        return $this->method === $method;
    }

    /**
     * @return bool
     */
    public function testIsGet()
    {
        return $this->isMethod('GET');
    }

    /**
     * @return bool
     */
    public function testIsPost()
    {
        return $this->isMethod('POST');
    }

    /**
     * @return bool
     */
    public function testIsPut()
    {
        return $this->isMethod('PUT');
    }

    /**
     * @return bool
     */
    public function testIsDelete()
    {
        return $this->isMethod('DELETE');
    }

    /**
     * @return bool
     */
    public function testIsPatch()
    {
        return $this->isMethod('PATCH');
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    public function testGetHeader($key)
    {
        if (!isset($this->headers[$key])) {
            return null;
        }
        return $this->headers[$key];
    }

    /**
     * @return array
     */
    public function testGetHeaders()
    {
        return $this->headers;
    }
}
