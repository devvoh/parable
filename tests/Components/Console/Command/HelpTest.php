<?php

namespace Parable\Tests\Components\Console\Command;

class HelpTest extends \Parable\Tests\Base
{
    /** @var \Parable\Console\App */
    protected $app;

    /** @var \Parable\Console\Command\Help */
    protected $helpCommand;

    protected function setUp()
    {
        parent::setUp();

        $this->app = \Parable\DI\Container::create(\Parable\Console\App::class);

        $this->helpCommand = new \Parable\Console\Command\Help();
        $this->app->addCommand($this->helpCommand);

        $this->helpCommand->prepare(
            $this->app,
            \Parable\DI\Container::create(\Parable\Console\Output::class),
            \Parable\DI\Container::create(\Parable\Console\Input::class),
            \Parable\DI\Container::create(\Parable\Console\Parameter::class)
        );
    }

    public function testRun()
    {
        $this->helpCommand->run();

        $expectedOutput = <<<EOT

\e[0;33m\e[0m                                     command-line tool\e[0m
---------------------------------------------------------------------\e[0m
Help screen - available commands:\e[0m

    \e[0;32mhelp\e[0m   \e[0mShows all commands available.\e[0m


EOT;
        $this->assertSame(
            $expectedOutput,
            $this->getActualOutputAndClean()
        );
    }
}
