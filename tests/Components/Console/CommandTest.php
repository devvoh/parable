<?php

namespace Parable\Tests\Components\Console;

class CommandTest extends \Parable\Tests\Base
{
    /** @var \Parable\Console\Command */
    protected $command;

    protected function setUp()
    {
        parent::setUp();

        $this->command = new \Parable\Console\Command();
    }

    public function testSetGetName()
    {
        $this->command->setName('name');
        $this->assertSame('name', $this->command->getName());
    }

    public function testSetGetDescription()
    {
        $this->command->setDescription('description');
        $this->assertSame('description', $this->command->getDescription());
    }

    public function testSetGetCallableAndRunCommand()
    {
        $callable = function () {
            return 'Yo!';
        };
        $this->command->setCallable($callable);

        $this->assertSame($callable, $this->command->getCallable());
        $this->assertSame('Yo!', $this->command->run());
    }

    public function testAddOptionAndGetOptions()
    {
        $this->command->addOption('option1', true, true, 'stupid');
        $this->command->addOption('option2', false, false, 'smart');

        $this->assertSame(
            [
                'option1' => [
                    'name'          => 'option1',
                    'required'      => true,
                    'valueRequired' => true,
                    'defaultValue'  => 'stupid',
                ],
                'option2' => [
                    'name'          => 'option2',
                    'required'      => false,
                    'valueRequired' => false,
                    'defaultValue'  => 'smart',
                ],
            ],
            $this->command->getOptions()
        );
    }
}
