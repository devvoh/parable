<?php

namespace Parable\Tests\Components\Framework;

class ConsoleAppTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\ConsoleApp */
    protected $consoleApp;

    public function setUp()
    {
        parent::setUp();

        $this->consoleApp = \Parable\DI\Container::createAll(\Parable\Framework\ConsoleApp::class);
    }

    public function testConsoleApp()
    {
        $this->consoleApp->run();

        $output = $this->getActualOutputAndClean();

        $this->assertContains("Parable " . \Parable\Framework\App::PARABLE_VERSION, $output);
    }
}
