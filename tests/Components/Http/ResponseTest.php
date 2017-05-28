<?php

namespace Parable\Tests\Components\Http;

class ResponseTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Response */
    protected $response;

    protected function setUp()
    {
        parent::setUp();

        $this->response = $this->createPartialMock(\Parable\Http\Response::class, ['terminate', 'redirect']);
        $this->response->__construct();
    }

    public function testGetHttpCode()
    {
        $this->assertSame(200, $this->response->getHttpCode());
    }

    public function testSetHttpCode()
    {
        $this->response->setHttpCode(404);
        $this->assertSame(404, $this->response->getHttpCode());
    }

    public function testGetHttpCodeText()
    {
        $this->assertSame("OK", $this->response->getHttpCodeText());

        $this->response->setHttpCode(404);
        $this->assertSame("Not Found", $this->response->getHttpCodeText());

        $this->response->setHttpCode(418);
        $this->assertSame("I'm a teapot", $this->response->getHttpCodeText());
    }

    public function testGetContentType()
    {
        $this->assertSame("text/html", $this->response->getContentType());
    }

    public function testSetContentType()
    {
        $this->response->setContentType('text/css');
        $this->assertSame("text/css", $this->response->getContentType());
    }

    public function testSetOutput()
    {
        $this->assertSame("text/html", $this->response->getContentType());

        $this->response->setOutput(new \Parable\Http\Output\Json());

        $this->assertSame("application/json", $this->response->getContentType());
    }

    public function testSetGetContent()
    {
        $this->response->setContent("This is content.");
        $this->assertSame("This is content.", $this->response->getContent());
    }

    public function testAppendAndPrependContent()
    {
        $this->response->setContent('yo2');
        $this->response->appendContent('yo3');
        $this->response->prependContent('yo1');

        $this->assertSame("yo1yo2yo3", $this->response->getContent());
    }

    public function testAppendAndPrependArrayContent()
    {
        $this->response->setContent(['array2']);
        $this->response->appendContent('array3');
        $this->response->prependContent('array1');

        $this->assertSame(
            [
                0 => 'array1',
                1 => 'array2',
                2 => 'array3',
            ],
            $this->response->getContent()
        );
    }

    public function testSend()
    {
        $this->response->setContent("This is content.");
        $this->response->send();

        $content = $this->getActualOutputAndClean();

        $this->assertSame("This is content.", $content);
    }

    public function testOutputBuffering()
    {
        $this->response->startOutputBuffer();
        echo "This should not show!";
        $this->response->returnOutputBuffer();

        $this->assertSame(null, $this->response->getContent());
    }

    public function testSetGetHeader()
    {
        $this->assertCount(0, $this->response->getHeaders());

        $this->response->setHeader('header1', 'yo1');
        $this->response->setHeader('header2', 'yo2');

        $this->assertCount(2, $this->response->getHeaders());
        $this->assertSame(
            [
                'header1' => 'yo1',
                'header2' => 'yo2',
            ],
            $this->response->getHeaders()
        );

        $this->assertSame("yo1", $this->response->getHeader("header1"));
    }

    public function testGetInvalidHeaderReturnsNull()
    {
        $this->assertNull($this->response->getHeader('la-dee-dah'));
    }

    public function testRedirect()
    {
        $this->response
            ->method('redirect')
            ->withAnyParameters()
            ->willReturnCallback(function () {
                $arguments = func_get_args();
                $this->assertSame('http://www.test.dev/redirected', $arguments[0]);
            });
        $this->response->redirect('http://www.test.dev/redirected');
    }
}
