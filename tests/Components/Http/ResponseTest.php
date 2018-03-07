<?php

namespace Parable\Tests\Components\Http;

class ResponseTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Response */
    protected $response;

    /** @var \Parable\Http\Response|\PHPUnit_Framework_MockObject_MockObject */
    protected $responseMock;

    protected function setUp()
    {
        parent::setUp();

        $this->responseMock = $this->createPartialMock(\Parable\Http\Response::class, ['terminate']);
        $this->responseMock->__construct(\Parable\DI\Container::get(\Parable\Http\Request::class));

        // Response should not actually terminate
        $this->response = \Parable\DI\Container::createAll(\Parable\Http\Response::class);
        $this->response->setShouldTerminate(false);
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

    public function testSetInvalidHttpCodeThrowsException()
    {
        $this->expectException(\Parable\Http\Exception::class);
        $this->expectExceptionMessage("Invalid HTTP code set: '9001'");

        $this->response->setHttpCode(9001);
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
        $this->assertSame(["This is content."], $this->response->getContent());
    }

    public function testSetGetContentAsString()
    {
        $this->response->setContent("This is content.");
        $this->assertSame("This is content.", $this->response->getContentAsString());
    }

    public function testAppendStringToContent()
    {
        $this->response->setContent("This is a thing ");
        $this->response->appendContent("and so is this.");

        $this->assertSame("This is a thing and so is this.", $this->response->getContentAsString());
    }

    public function testAppendArrayToContent()
    {
        $this->response->setContent("This is a thing ");
        $this->response->appendContent(["but" => "this is an array"]);

        $this->assertSame(
            "This is a thing Array\n(\n    [but] => this is an array\n)",
            $this->response->getContentAsString()
        );
    }

    public function testClearContent()
    {
        $this->response->setContent("This is content.");
        $this->assertSame("This is content.", $this->response->getContentAsString());

        $this->response->clearContent();
        $this->response->appendContent("New!");

        $this->assertSame("New!", $this->response->getContentAsString());
    }

    public function testAppendAndPrependContent()
    {
        $this->response->setContent('yo2');
        $this->response->appendContent('yo3');
        $this->response->prependContent('yo1');

        $this->assertSame("yo1yo2yo3", $this->response->getContentAsString());
    }

    public function testAppendAndPrependContentIsCorrectArray()
    {
        $this->response->setContent(['yo2']);
        $this->response->appendContent('yo3');
        $this->response->prependContent('yo1');

        $this->assertSame(
            ["yo1", "yo2", "yo3"],
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

    public function testSendWithJsonAndOutputBufferingEnabled()
    {
        $this->response->startOutputBuffer();
        $this->response->setOutput(new \Parable\Http\Output\Json());
        $this->response->setContent([
            'test' => 1,
        ]);
        $this->response->send();

        $content = $this->getActualOutputAndClean();

        $this->assertSame('{"test":1}', $content);
    }

    public function testFlushOutputBuffer()
    {
        $this->response->startOutputBuffer();

        echo "YO YO YO TESTING THIS THING";

        $this->response->flushOutputBuffer();

        $this->assertSame("YO YO YO TESTING THIS THING", $this->response->getContentAsString());
    }

    public function testFlushOutputBufferOnlyFlushesLatestBuffer()
    {
        $this->response->startOutputBuffer();
        echo "YO YO YO TESTING THIS THING";

        $this->response->startOutputBuffer();
        echo "WHAT IS THIS";

        $this->response->flushOutputBuffer();

        $this->assertSame("WHAT IS THIS", $this->response->getContentAsString());

        $this->response->flushOutputBuffer();

        $this->assertSame("WHAT IS THISYO YO YO TESTING THIS THING", $this->response->getContentAsString());
    }

    public function testFlushAllOutputBuffersFlushesAll()
    {
        $this->response->startOutputBuffer();
        echo "YO YO YO TESTING THIS THING";

        $this->response->startOutputBuffer();
        echo "WHAT IS THIS";

        $this->response->flushAllOutputBuffers();

        $this->assertSame("WHAT IS THISYO YO YO TESTING THIS THING", $this->response->getContentAsString());
    }

    public function testIsOutputBufferingEnabled()
    {
        $this->assertFalse($this->response->isOutputBufferingEnabled());

        $this->response->startOutputBuffer();
        $this->response->startOutputBuffer();
        $this->response->startOutputBuffer();

        $this->assertTrue($this->response->isOutputBufferingEnabled());

        $this->response->flushOutputBuffer();
        $this->response->flushOutputBuffer();

        // There's still one level left
        $this->assertTrue($this->response->isOutputBufferingEnabled());

        $this->response->flushOutputBuffer();

        // And now there's no levels left
        $this->assertFalse($this->response->isOutputBufferingEnabled());

        $this->response->returnAllOutputBuffers();
    }

    public function testSendClosesOutputbuffer()
    {
        $this->response->startOutputBuffer();
        echo "1... ";

        $this->response->setContent("This is content.");

        echo "2... ";

        $this->response->send();

        $content = $this->getActualOutputAndClean();

        $this->assertSame("1... 2... This is content.", $content);
    }

    public function testOutputBuffering()
    {
        $this->response->startOutputBuffer();
        echo "This should not show!";
        $this->response->returnOutputBuffer();

        $this->assertSame([], $this->response->getContent());
        $this->assertSame("", $this->response->getContentAsString());
    }

    public function testReturnOutputBufferReturnsEmptyStringIfNotStarted()
    {
        $this->assertSame("", $this->response->returnOutputBuffer());
    }

    public function testReturnAllOutputBufferReturnsEmptyStringIfNotStarted()
    {
        $this->assertSame("", $this->response->returnAllOutputBuffers());
    }

    public function testOutputBufferingChecking()
    {
        $this->assertFalse($this->response->isOutputBufferingEnabled());

        $this->response->startOutputBuffer();

        $this->assertTrue($this->response->isOutputBufferingEnabled());

        $this->response->returnOutputBuffer();

        $this->assertFalse($this->response->isOutputBufferingEnabled());
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

    public function testSetHeaders()
    {
        $this->assertCount(0, $this->response->getHeaders());

        $this->response->setHeaders([
            "header1" => "yo1",
            "header2" => "yo2",
        ]);

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

    public function testSetAndRemoveHeader()
    {
        $this->response->setHeaders([
            "remove" => "me",
            "leave" => "me",
        ]);

        $this->assertCount(2, $this->response->getHeaders());

        $this->response->removeHeader("remove");

        $this->assertCount(1, $this->response->getHeaders());
    }

    public function testClearHeaders()
    {
        $this->response->setHeaders([
            "remove" => "me",
            "leave" => "me",
        ]);

        $this->assertCount(2, $this->response->getHeaders());

        $this->response->clearHeaders();

        $this->assertCount(0, $this->response->getHeaders());
    }

    public function testGetInvalidHeaderReturnsNull()
    {
        $this->assertNull($this->response->getHeader('la-dee-dah'));
    }

    public function testRedirect()
    {
        // The only way to test this is to see if terminate is called
        $this->responseMock->expects($this->once())->method('terminate');
        $this->responseMock->redirect('http://www.test.dev/redirected');
    }

    /**
     * @dataProvider dpDataTypes
     */
    public function testGetContentAsStringHandlesDataTypes($data, $expectedStringValue)
    {
        $this->response->appendContent($data);
        $this->assertSame($expectedStringValue, $this->response->getContentAsString());
    }

    public function dpDataTypes()
    {
        return [
            ["string", "string"],
            [1337, "1337"],
            [true, "1"],
            [new \stdClass(), "stdClass Object\n(\n)"],
            [["an" => "array value"], "Array\n(\n    [an] => array value\n)"],
        ];
    }
}
