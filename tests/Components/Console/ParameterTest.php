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

    public function testParseParametersWorkedCorrectly()
    {
        $this->parameter->setCommandOptions([
            "option" => new \Parable\Console\Parameter\Option("option"),
            "key"    => new \Parable\Console\Parameter\Option("key"),
        ]);
        $this->parameter->setCommandArguments([
            new \Parable\Console\Parameter\Argument("arg1"),
        ]);

        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            '--option',
            'argument',
            '--key=value2',
        ]);

        $this->parameter->checkCommandOptions();
        $this->parameter->checkCommandArguments();

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertSame('command-to-run', $this->parameter->getCommandName());

        $this->assertTrue($this->parameter->getOption('option'));
        $this->assertSame("value2", $this->parameter->getOption('key'));

        $this->assertSame(
            [
                "command-to-run",
                "--option",
                "argument",
                "--key=value2",
            ],
            $this->parameter->getParameters()
        );
    }

    public function testCommandNameIsReturnedProperlyIfGiven()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertSame('command-to-run', $this->parameter->getCommandName());
    }

    public function testGetInvalidOptionReturnsNull()
    {
        $this->assertNull($this->parameter->getOption('la-dee-dah'));
    }

    public function testCommandNameIsNullIfNotGiven()
    {
        $this->parameter->setParameters([
            './test.php',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertNull($this->parameter->getCommandName());
    }

    public function testCommandNameIsNullIfNotGivenButThereIsAnOptionGiven()
    {
        $this->parameter->setParameters([
            './test.php',
            '--option',
        ]);

        $this->assertSame('./test.php', $this->parameter->getScriptName());
        $this->assertNull($this->parameter->getCommandName());
    }

    public function testThrowsExceptionWhenOptionIsGivenButValueRequiredNotGiven()
    {
        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Option '--option' requires a value, which is not provided.");

        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            '--option',
        ]);

        $this->parameter->setCommandOptions([
            "option" => new \Parable\Console\Parameter\Option(
                "option",
                \Parable\Console\Parameter::PARAMETER_OPTIONAL,
                \Parable\Console\Parameter::OPTION_VALUE_REQUIRED
            ),
        ]);

        $this->parameter->checkCommandOptions();
    }

    public function testOptionIsGivenAndValueRequiredAlsoGivenWorksProperly()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            '--option=option-value'
        ]);

        $this->parameter->setCommandOptions([
            "option" => new \Parable\Console\Parameter\Option(
                "option",
                \Parable\Console\Parameter::PARAMETER_OPTIONAL,
                \Parable\Console\Parameter::OPTION_VALUE_REQUIRED
            ),
        ]);
        $this->parameter->checkCommandOptions();

        $this->assertSame('option-value', $this->parameter->getOption('option'));
    }

    public function testThrowsExceptionWhenRequiredOptionIsMissing()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
        ]);

        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Required option '--option' not provided.");

        $this->parameter->setCommandOptions([
            "option" => new \Parable\Console\Parameter\Option(
                "option",
                \Parable\Console\Parameter::PARAMETER_REQUIRED
            ),
        ]);
        $this->parameter->checkCommandOptions();
    }

    public function testRequiredArgumentThrowsException()
    {
        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Required argument with index #1 'numero2' not provided.");

        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            'arg1',
        ]);
        $this->parameter->setCommandArguments([
            new \Parable\Console\Parameter\Argument("numero1", \Parable\Console\Parameter::PARAMETER_REQUIRED),
            new \Parable\Console\Parameter\Argument("numero2", \Parable\Console\Parameter::PARAMETER_REQUIRED),
        ]);
        $this->parameter->checkCommandArguments();
    }

    public function testGetArgumentReturnsAppropriateValues()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            'arg1',
            'arg2',
        ]);

        $this->parameter->setCommandArguments([
            new \Parable\Console\Parameter\Argument("numero1", \Parable\Console\Parameter::PARAMETER_REQUIRED),
            new \Parable\Console\Parameter\Argument("numero2", \Parable\Console\Parameter::PARAMETER_REQUIRED, 12),
            new \Parable\Console\Parameter\Argument("numero3", \Parable\Console\Parameter::PARAMETER_OPTIONAL, 24),
        ]);

        $this->parameter->checkCommandArguments();

        $this->assertSame("arg1", $this->parameter->getArgument("numero1"));
        $this->assertSame("arg2", $this->parameter->getArgument("numero2"));
        $this->assertSame(24, $this->parameter->getArgument("numero3"));
    }

    public function testInvalidArgumentReturnsNull()
    {
        $this->assertNull($this->parameter->getArgument("totally not"));
    }

    public function testMultipleOptionParameters()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            '--option1=value1',
            '--option2',
            '--option3=value3',
        ]);

        $this->parameter->setCommandOptions([
            new \Parable\Console\Parameter\Option("option1", \Parable\Console\Parameter::PARAMETER_REQUIRED, \Parable\Console\Parameter::OPTION_VALUE_REQUIRED),
            new \Parable\Console\Parameter\Option("option2", \Parable\Console\Parameter::PARAMETER_REQUIRED),
            new \Parable\Console\Parameter\Option("option3", \Parable\Console\Parameter::PARAMETER_OPTIONAL, \Parable\Console\Parameter::OPTION_VALUE_REQUIRED),
        ]);

        $this->parameter->checkCommandOptions();

        $this->assertSame(
            [
                'option1' => 'value1',
                'option2' => true,
                'option3' => 'value3',
            ],
            $this->parameter->getOptions()
        );
    }

    public function testArgumentsWorkProperly()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            'argument1',
            'argument2 is a string',
            '--option1=value1',
            'argument3!',
            '--option2=value2',
            'argument4',
            'argument5',
        ]);

        $this->parameter->setCommandOptions([
            new \Parable\Console\Parameter\Option("option1"),
            new \Parable\Console\Parameter\Option("option2"),
        ]);
        $this->parameter->setCommandArguments([
            new \Parable\Console\Parameter\Argument("brg1", \Parable\Console\Parameter::PARAMETER_REQUIRED),
            new \Parable\Console\Parameter\Argument("arg2", \Parable\Console\Parameter::PARAMETER_OPTIONAL),
            new \Parable\Console\Parameter\Argument("arg3", \Parable\Console\Parameter::PARAMETER_OPTIONAL),
            new \Parable\Console\Parameter\Argument("arg4", \Parable\Console\Parameter::PARAMETER_OPTIONAL),
            new \Parable\Console\Parameter\Argument("arg5", \Parable\Console\Parameter::PARAMETER_OPTIONAL),
        ]);

        $this->parameter->checkCommandOptions();
        $this->parameter->checkCommandArguments();

        $this->assertSame(
            [
                'option1' => 'value1',
                'option2' => 'value2',
            ],
            $this->parameter->getOptions()
        );
        $this->assertSame(
            [
                'brg1' => 'argument1',
                'arg2' => 'argument2 is a string',
                'arg3' => 'argument3!',
                'arg4' => 'argument4',
                'arg5' => 'argument5',
            ],
            $this->parameter->getArguments()
        );
    }

    public function testSetCommandOptionsWithArrayThrowsException()
    {
        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Options must be instances of Parameter\Option. invalid_option is not.");

        $this->parameter->setCommandOptions(["invalid_option" => []]);
    }

    public function testSetCommandArgumentsWithArrayThrowsException()
    {
        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Arguments must be instances of Parameter\Argument. The item at index 0 is not.");

        $this->parameter->setCommandArguments([[]]);
    }

    public function testEnableDisableCommandNameKeepsArgumentOrderValid()
    {
        $this->parameter->setParameters([
            './test.php',
            'command-to-run',
            'argument1',
        ]);

        $this->parameter->setCommandArguments([
            new \Parable\Console\Parameter\Argument("arg1", \Parable\Console\Parameter::PARAMETER_OPTIONAL),
            new \Parable\Console\Parameter\Argument("arg2", \Parable\Console\Parameter::PARAMETER_OPTIONAL),
        ]);

        $this->parameter->checkCommandArguments();

        $this->assertSame(
            [
                "arg1" => "argument1",
                "arg2" => null,
            ],
            $this->parameter->getArguments()
        );

        $this->parameter->disableCommandName()->checkCommandArguments();

        $this->assertSame(
            [
                "arg1" => "command-to-run",
                "arg2" => "argument1",
            ],
            $this->parameter->getArguments()
        );

        $this->parameter->enableCommandName()->checkCommandArguments();

        $this->assertSame(
            [
                "arg1" => "argument1",
                "arg2" => null,
            ],
            $this->parameter->getArguments()
        );
    }

    public function testParameterRequiredOnlyAcceptConstantValues()
    {
        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Required must one of the PARAMETER_* constants.");

        $option = new \Parable\Console\Parameter\Option("test", 418);
    }

    public function testParameterValueRequiredOnlyAcceptConstantValues()
    {
        $this->expectException(\Parable\Console\Exception::class);
        $this->expectExceptionMessage("Value required must one of the OPTION_VALUE_* constants.");

        $option = new \Parable\Console\Parameter\Option(
            "test",
            \Parable\Console\Parameter::PARAMETER_REQUIRED,
            418
        );
    }
}
