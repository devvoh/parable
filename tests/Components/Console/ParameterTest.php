<?php

namespace Parable\Tests\Components\Console;

class ParameterTest extends \Parable\Tests\Base
{
    /** @var \Parable\Console\Parameter */
    protected $parameter;

    protected function setUp()
    {
        parent::setUp();

        $this->parameter = new \Parable\Console\Parameter();
    }

    public function testParseArgumentsWorkedCorrectly()
    {
        $this->parameter->setArguments([
            './test.php',
            'command-to-run',
            '--option',
            '--key',
            'value',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertSame('command-to-run', $this->parameter->getCommandName());

        $this->assertTrue($this->parameter->getOption('option'));
        $this->assertSame("value", $this->parameter->getOption('key'));
    }

    public function testCommandNameIsReturnedProperlyIfGiven()
    {
        $this->parameter->setArguments([
            './test.php',
            'command-to-run',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertSame('command-to-run', $this->parameter->getCommandName());
    }

    public function testCommandNameIsNullIfNotGiven()
    {
        $this->parameter->setArguments([
            './test.php',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertNull($this->parameter->getCommandName());
    }

    public function testCommandNameIsNullIfNotGivenButThereIsAnOptionGiven()
    {
        $this->parameter->setArguments([
            './test.php',
            '--option',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertNull($this->parameter->getCommandName());
    }

    public function testThrowsExceptionWhenOptionIsGivenButValueRequiredNotGiven()
    {
        $this->parameter->setArguments([
            './test.php',
            'command-to-run',
            '--option',
        ]);

        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Option '--option' requires a value, which is not provided.");

        $this->parameter->setOptions([
            ['name' => 'option', 'required' => false, 'valueRequired' => true],
        ]);
        $this->parameter->checkOptions();
    }

    public function testOptionIsGivenAndValueRequiredAlsoGivenWorksProperly()
    {
        $this->parameter->setArguments([
            './test.php',
            'command-to-run',
            '--option',
            'option-value'
        ]);

        $this->parameter->setOptions([
            ['name' => 'option', 'required' => false, 'valueRequired' => true],
        ]);
        $this->parameter->checkOptions();

        $this->assertSame('option-value', $this->parameter->getOption('option'));
    }

    public function testThrowsExceptionWhenRequiredOptionIsMissing()
    {
        $this->parameter->setArguments([
            './test.php',
            'command-to-run',
        ]);

        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Required option '--option' not provided.");
        $this->parameter->setOptions([
            ['name' => 'option', 'required' => true],
        ]);
        $this->parameter->checkOptions();
    }

    public function testMultipleParameters()
    {
        $this->parameter->setArguments([
            './test.php',
            'command-to-run',
            '--option1',
            'value1',
            '--option2',
            '--option3',
            'value3',
        ]);

        $this->parameter->setOptions([
            ['name' => 'option1', 'required' => true, 'valueRequired' => true],
            ['name' => 'option2', 'required' => true],
            ['name' => 'option3', 'required' => true, 'valueRequired' => true],
        ]);

        $this->parameter->checkOptions();

        $this->assertSame(
            [
                'option1' => 'value1',
                'option2' => true,
                'option3' => 'value3',
            ],
            $this->parameter->getOptions()
        );
    }
}
