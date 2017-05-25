<?php

namespace Parable\Tests\TestClasses;

class CommandCallsCommand extends \Parable\Console\Command
{
    protected $name = 'calling-command';
    protected $description = 'This is a test command.';

    public function run()
    {
        $return = $this->runCommand(new \Parable\Tests\TestClasses\Command());
        return "Command returned: {$return}";
    }
}
