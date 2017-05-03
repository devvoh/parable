<?php

namespace Command;

class HelloWorld extends \Parable\Console\Command
{
    protected $name = 'helloworld';
    protected $description = 'This command just wants to say hi.';

    public function run()
    {
        $this->output->writeln('<blue>Hello, world!</blue>');
    }
}