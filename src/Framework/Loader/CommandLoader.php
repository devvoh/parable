<?php

namespace Parable\Framework\Loader;

class CommandLoader
{
    /** @var \Parable\Console\App */
    protected $consoleApp;

    public function __construct(
        \Parable\Console\App $consoleApp
    ) {
        $this->consoleApp = $consoleApp;
    }

    /**
     * Add all commands passed to the console app.
     *
     * @param string[] $commandClasses
     *
     * @throws \Parable\DI\Exception
     */
    public function load(array $commandClasses)
    {
        foreach ($commandClasses as $commandClass) {
            $command = \Parable\DI\Container::get($commandClass);
            $this->consoleApp->addCommand($command);
        }
    }
}
