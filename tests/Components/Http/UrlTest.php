<?php

namespace Parable\Tests\Components\Http;

class UrlTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Url */
    protected $url;

    protected function setUp()
    {
        parent::setUp();

        $GLOBALS['_SERVER'] = [
            'REQUEST_SCHEME' => 'http',
            'HTTP_HOST'      => 'www.test.dev',
            'SCRIPT_NAME'    => '/test/public/index.php',
        ];
        $GLOBALS['_GET'] = [
            'url' => 'this/was/requested',
        ];

        $this->url = \Parable\DI\Container::createAll(\Parable\Http\Url::class);
    }

    public function testGetBaseUrl()
    {
        $this->assertSame('http://www.test.dev/test', $this->url->getBaseurl());
    }

    public function testGetUrl()
    {
        $this->assertSame('http://www.test.dev/test/stuff/goes/here', $this->url->getUrl('stuff/goes/here'));
    }

    public function testGetCurrentUrl()
    {
        $this->assertSame('this/was/requested', $this->url->getCurrentUrl());
    }

    public function testGetCurrentUrlReturnsEmptyUrlIfNoUrlKnown()
    {
        unset($GLOBALS['_GET']['url']);
        $this->assertSame('/', $this->url->getCurrentUrl());
    }

    public function testGetCurrentUrlFull()
    {
        $this->assertSame('http://www.test.dev/test/this/was/requested', $this->url->getCurrentUrlFull());
    }
}
