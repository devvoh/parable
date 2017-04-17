<?php

namespace Command;

class HelloWorld extends \Parable\Console\Command
{
    protected $name = 'helloworld';
    protected $description = 'This command just wants to say hi.';

    public function run(
        \Parable\Console\Output $output,
        \Parable\Console\Input $input,
        \Parable\Console\Parameter $parameter
    ) {
        $output->writeln('<green>Hello, world!</green>');
    }
}