<?php

namespace Parable\Console;

class Parameter
{
    /** Used to designate a parameter as existing but without value */
    const PARAMETER_EXISTS = '__parameter_exists__';

    /** @var array */
    protected $rawArguments = [];

    /** @var string */
    protected $scriptName;

    /** @var string */
    protected $commandName;

    /** @var array */
    protected $arguments = [];

    /** @var array */
    protected $options = [];

    public function __construct()
    {
        $this->setArguments($_SERVER["argv"]);
    }

    /**
     * @param array $arguments
     *
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->rawArguments = $arguments;
        $this->parseArguments();
        return $this;
    }

    /**
     * @return $this
     */
    public function parseArguments()
    {
        // Reset all previously gathered data
        $this->reset();

        // Extract the scriptName
        $this->scriptName = array_shift($this->rawArguments);

        // Extract the commandName
        if (isset($this->rawArguments[0])
            && !empty($this->rawArguments[0])
            && strpos($this->rawArguments[0], '--') === false
        ) {
            $this->commandName = array_shift($this->rawArguments);
        }

        $optionName = null;
        foreach ($this->rawArguments as $key => $argument) {
            if (substr($argument, 0, 2) == '--') {
                $optionName = ltrim($argument, '-');
                $this->arguments[$optionName] = static::PARAMETER_EXISTS;
            } elseif ($optionName !== null) {
                $this->arguments[$optionName] = $argument;
                $optionName = null;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * @return null|string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Checks the options set against the parameters set. Takes into account whether an option is required
     * to be passed or not, or a value is required if it's passed, or sets the defaultValue if given and necessary.
     *
     * @throws \Parable\Console\Exception
     */
    public function checkOptions()
    {
        foreach ($this->options as $option) {
            // Check if required option is actually passed
            if (isset($option['required'])
                && $option['required']
                && !array_key_exists($option['name'], $this->arguments)
            ) {
                throw new \Parable\Console\Exception("Required option '--{$option['name']}' not provided.");
            }

            // Check if non-required but passed option requires a value
            if (array_key_exists($option['name'], $this->arguments)
                && isset($option['valueRequired'])
                && $option['valueRequired']
                && (!$this->arguments[$option['name']] || $this->arguments[$option['name']] == static::PARAMETER_EXISTS)
            ) {
                throw new \Parable\Console\Exception(
                    "Option '--{$option['name']}' requires a value, which is not provided."
                );
            }

            // Set default value if defaultValue is set and the option is either passed without value or not passed
            if (isset($option['defaultValue'])
                && $option['defaultValue']
                && (
                    !array_key_exists($option['name'], $this->arguments)
                    || $this->arguments[$option['name']] == static::PARAMETER_EXISTS
                )
            ) {
                $this->arguments[$option['name']] = $option['defaultValue'];
            }
        }
    }

    /**
     * Returns null if the value doesn't exist. Otherwise, it's whatever was passed to it or set
     * as a default value.
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->arguments)) {
            return null;
        }
        if ($this->arguments[$name] == static::PARAMETER_EXISTS) {
            return true;
        }
        return $this->arguments[$name];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $returnArray = [];
        foreach ($this->arguments as $key => $value) {
            $returnArray[$key] = $this->getOption($key);
        }
        return $returnArray;
    }

    /**
     * @return $this
     */
    protected function reset()
    {
        $this->arguments   = [];
        $this->scriptName  = null;
        $this->commandName = null;

        return $this;
    }
}
