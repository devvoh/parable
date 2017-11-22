<?php

namespace Parable\Console;

class Command
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var callable */
    protected $callable;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $arguments = [];

    /** @var \Parable\Console\App */
    protected $app;

    /** @var \Parable\Console\Output */
    protected $output;

    /** @var \Parable\Console\Input */
    protected $input;

    /** @var \Parable\Console\Parameter */
    protected $parameter;

    /**
     * Set the command name.
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
     * Return the command name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the command description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Return the command description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the callable to be run when the command is run.
     *
     * @param callable $callable
     *
     * @return $this
     */
    public function setCallable(callable $callable)
    {
        $this->callable = $callable;
        return $this;
    }

    /**
     * Return the callable.
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Add an option for this command.
     *
     * @param string $name
     * @param bool   $required
     * @param bool   $valueRequired
     * @param mixed  $defaultValue
     *
     * @return $this
     */
    public function addOption($name, $required = false, $valueRequired = false, $defaultValue = null)
    {
        $this->options[$name] = [
            'name'          => $name,
            'required'      => $required,
            'valueRequired' => $valueRequired,
            'defaultValue'  => $defaultValue,
        ];
        return $this;
    }

    /**
     * Return all options for this command.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add an argument for this command.
     *
     * @param string $name
     * @param bool   $required
     * @param mixed  $defaultValue
     *
     * @return $this
     */
    public function addArgument($name, $required = false, $defaultValue = null)
    {
        $this->arguments[] = [
            'name'         => $name,
            'required'     => $required,
            'defaultValue' => $defaultValue,
        ];
        return $this;
    }

    /**
     * Return all arguments for this command.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Prepare the command, setting all classes the command is dependant on.
     *
     * @param \Parable\Console\App       $app
     * @param \Parable\Console\Output    $output
     * @param \Parable\Console\Input     $input
     * @param \Parable\Console\Parameter $parameter
     *
     * @return $this
     */
    public function prepare(
        \Parable\Console\App $app,
        \Parable\Console\Output $output,
        \Parable\Console\Input $input,
        \Parable\Console\Parameter $parameter
    ) {
        $this->app       = $app;
        $this->output    = $output;
        $this->input     = $input;
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Run the callable if it's set. This can be overridden by implementing the run method on a Command class.
     *
     * @return mixed
     */
    public function run()
    {
        $callable = $this->getCallable();
        if (is_callable($callable)) {
            return $callable($this->app, $this->output, $this->input, $this->parameter);
        }
        return false;
    }

    /**
     * Run another command from the current command, passing parameters as an array.
     *
     * @param \Parable\Console\Command $command
     * @param array                    $parameters
     * @return mixed
     */
    protected function runCommand(\Parable\Console\Command $command, array $parameters = [])
    {
        $parameter = new \Parable\Console\Parameter();
        $parameter->setParameters($parameters);

        $command->prepare($this->app, $this->output, $this->input, $parameter);

        return $command->run();
    }
}
