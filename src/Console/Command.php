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

    /**
     * @param \Parable\Console\App $app
     *
     * @return $this
     */
    public function setApp(\Parable\Console\App $app)
    {
        $this->app = $app;
        return $this;
    }

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
     * @param \Parable\Console\Output    $output
     * @param \Parable\Console\Input     $input
     * @param \Parable\Console\Parameter $parameter
     *
     * @return $this|mixed
     */
    public function run(
        \Parable\Console\Output $output,
        \Parable\Console\Input $input,
        \Parable\Console\Parameter $parameter
    ) {
        $callable = $this->getCallable();
        if (is_callable($callable)) {
            return $callable($output, $input, $parameter);
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
        $this->options[] = [
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
