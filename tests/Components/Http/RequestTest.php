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

        $this->mockProperty($this->request, 'headers', ['testkey' => 'testvalue']);
    }

    public function testGetMethod()
    {
        $this->assertSame("GET", $this->request->getMethod());
    }

    public function testIsMethod()
    {
        $this->assertTrue($this->request->isMethod('GET'));
        $this->assertfalse($this->request->isMethod('POST'));
        $this->assertfalse($this->request->isMethod('PUT'));
        $this->assertfalse($this->request->isMethod('DELETE'));
        $this->assertfalse($this->request->isMethod('PATCH'));
    }

    public function testIsGet()
    {
        $this->assertTrue($this->request->isGet());
    }

    public function testIsPost()
    {
        $this->assertFalse($this->request->isPost());
    }

    public function testIsPut()
    {
        $this->assertFalse($this->request->isPut());
    }

    public function testIsDelete()
    {
        $this->assertFalse($this->request->isDelete());
    }

    public function testIsPatch()
    {
        $this->assertFalse($this->request->isPatch());
    }

    public function testHeader()
    {
        $this->assertSame('testvalue', $this->request->getHeader('testkey'));
    }

    public function testGetHeaders()
    {
        $this->assertSame(
            [
                'testkey' => 'testvalue',
            ],
            $this->request->getHeaders()
        );
    }
}
