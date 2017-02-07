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

    /** @var string */
    protected $commands = [];

    /** @var string */
    protected $defaultCommand;

    /** @var bool */
    protected $onlyCommand = false;

    /**
     * @param \Parable\Console\Output $output
     * @param \Parable\Console\Input $input
     * @param \Parable\Console\Parameter $parameter
     */
    public function __construct(
        \Parable\Console\Output $output,
        \Parable\Console\Input $input,
        \Parable\Console\Parameter $parameter
    ) {
        $this->output    = $output;
        $this->input     = $input;
        $this->parameter = $parameter;

        set_exception_handler(function(\Exception $e) {
            $this->output->writeError($e->getMessage());
        });
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Parable\Console\Command $command
     *
     * @return $this
     */
    public function addCommand(\Parable\Console\Command $command)
    {
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * @param string $commandName
     * @param bool   $onlyCommand
     *
     * @return $this
     */
    public function setDefaultCommand($commandName, $onlyCommand = false)
    {
        $this->defaultCommand = $commandName;
        $this->onlyCommand = $onlyCommand;
        return $this;
    }

    /**
     * @param string $commandName
     *
     * @return null
     */
    public function getCommand($commandName)
    {
        if (isset($this->commands[$commandName])) {
            return $this->commands[$commandName];
        }
        return null;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function run()
    {
        $command = null;
        if ($this->defaultCommand && $this->onlyCommand) {
            $command = $this->getCommand($this->defaultCommand);
        } else {
            $commandName = $this->parameter->getCommandName();
            if (!$commandName && $this->defaultCommand) {
                $command = $this->getCommand($this->defaultCommand);
            } elseif ($commandName) {
                $command = $this->getCommand($commandName);
            }
        }

        if (!$command) {
            throw new \Parable\Console\Exception('No valid command found.');
        }

        $callable = $command->getCallable();
        $callable($this->output, $this->input, $this->parameter);
        return $this;
    }
}
