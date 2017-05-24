<?php

namespace Parable\Tests\TestClasses;

class Command extends \Parable\Console\Command
{
    protected $name = 'testcommand';
    protected $description = 'This is a test command.';

    public function run()
    {
        return 'OK';
    }
}
