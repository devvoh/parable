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

    /** @var \Parable\Console\App */
    protected $app;

    /** @var \Parable\Console\Output */
    protected $output;

    /** @var \Parable\Console\Input */
    protected $input;

    /** @var \Parable\Console\Parameter */
    protected $parameter;

    /**
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
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
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @param \Parable\Console\App       $app
     * @param \Parable\Console\Output    $output
     * @param \Parable\Console\Input     $input
     * @param \Parable\Console\Parameter $parameter
     *
     * @return $this|mixed
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
    }

    /**
     * @return $this|mixed
     */
    public function run()
    {
        $callable = $this->getCallable();
        if (is_callable($callable)) {
            return $callable($this->app, $this->output, $this->input, $this->parameter);
        }
        return $this;
    }

    /**
     * @param string      $name
     * @param bool        $required
     * @param bool        $valueRequired
     * @param mixed|null  $defaultValue
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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
