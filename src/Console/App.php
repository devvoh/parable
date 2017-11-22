<?php

namespace Parable\Console;

class App
{
    /** @var \Parable\Console\Output */
    protected $output;

    /** @var \Parable\Console\Input */
    protected $input;

    /** @var \Parable\Console\Parameter */
    protected $parameter;

    /** @var string */
    protected $name;

    /** @var \Parable\Console\Command[] */
    protected $commands = [];

    /** @var string */
    protected $defaultCommand;

    /** @var bool */
    protected $defaultCommandOnly = false;

    public function __construct(
        \Parable\Console\Output $output,
        \Parable\Console\Input $input,
        \Parable\Console\Parameter $parameter
    ) {
        $this->output    = $output;
        $this->input     = $input;
        $this->parameter = $parameter;

        set_exception_handler(function ($e) {
            // @codeCoverageIgnoreStart
            $this->output->writeError($e->getMessage());
            // @codeCoverageIgnoreEnd
        });
    }

    /**
     * Set the application name..
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return the application name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add a command to the application.
     *
     * @param \Parable\Console\Command $command
     *
     * @return $this
     */
    public function addCommand(\Parable\Console\Command $command)
    {
        $command->prepare($this, $this->output, $this->input, $this->parameter);
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * Add an array of commands to the application.
     *
     * @param \Parable\Console\Command[] $commands
     *
     * @return $this
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
        return $this;
    }

    /**
     * Set the default command to use if no command is given (by name).
     *
     * @param string $commandName
     * @param bool   $defaultCommandOnly
     *
     * @return $this
     */
    public function setDefaultCommandByName($commandName, $defaultCommandOnly = false)
    {
        $this->defaultCommand     = $commandName;
        $this->defaultCommandOnly = $defaultCommandOnly;
        return $this;
    }

    /**
     * Set the default command to use if no command is given.
     *
     * @param string $commandName
     * @param bool   $defaultCommandOnly
     *
     * @return $this
     */
    public function setDefaultCommand(\Parable\Console\Command $command, $defaultCommandOnly = false)
    {
        $this->setDefaultCommandByName($command->getName(), $defaultCommandOnly);
        return $this;
    }

    /**
     * Return the command by name if it's set on the application.
     *
     * @param string $commandName
     *
     * @return null|\Parable\Console\Command
     */
    public function getCommand($commandName)
    {
        if (isset($this->commands[$commandName])) {
            return $this->commands[$commandName];
        }
        return null;
    }

    /**
     * Return all commands set on the application.
     *
     * @return \Parable\Console\Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Run the application.
     *
     * @return mixed
     * @throws \Parable\Console\Exception
     */
    public function run()
    {
        $defaultCommand = null;
        $command        = null;
        if ($this->defaultCommand) {
            $defaultCommand = $this->getCommand($this->defaultCommand);
        }
        if (!$this->defaultCommandOnly) {
            $commandName = $this->parameter->getCommandName();
            if ($commandName) {
                $command = $this->getCommand($commandName);
            }
        }

        // Use $command or $defaultCommand, since they're mutually exclusive
        $command = $command ?: $defaultCommand;

        if (!$command) {
            throw new \Parable\Console\Exception('No valid commands found.');
        }

        $this->parameter->setCommandArguments($command->getArguments());
        $this->parameter->checkArguments();

        $this->parameter->setCommandOptions($command->getOptions());
        $this->parameter->checkOptions();

        return $command->run();
    }
}
