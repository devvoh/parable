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

    /** @var array */
    protected $arguments = [];

    /** @var array */
    protected $options = [];

    public function __construct()
    {
        $this->rawArguments = $_SERVER["argv"];
        $this->parseArguments();
    }

    /**
     * @return $this
     */
    public function parseArguments()
    {
        $this->scriptName = array_shift($this->rawArguments);

        $argumentsCopy = $this->rawArguments;

        $optionName = null;
        foreach ($argumentsCopy as $key => $argument) {
            if (substr($argument, 0, 2) == '--') {
                $optionName = $this->trimDashes($argument);
                $this->arguments[$optionName] = static::PARAMETER_EXISTS;
            } elseif ($optionName !== null) {
                $this->arguments[$optionName] = $argument;
            }
        }

        return $this;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function trimDashes($string)
    {
        return ltrim($string, '-');
    }

    /**
     * @return null|string
     */
    public function getCommandName()
    {
        if (count($this->rawArguments) >= 1) {
            return $this->rawArguments[0];
        }
        return null;
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
     * @throws \Parable\Console\Exception
     */
    public function checkOptions()
    {
        foreach ($this->options as $option) {
            // Check if required option is actually passed
            if (
                $option['required']
                && !array_key_exists($option['name'], $this->arguments)
            ) {
                throw new \Parable\Console\Exception("Required option '--{$option['name']}' not provided.");
            }

            // Check if non-required but passed option requires a value
            if (
                array_key_exists($option['name'], $this->arguments)
                && $option['valueRequired']
                && (!$this->arguments[$option['name']] || $this->arguments[$option['name']] == static::PARAMETER_EXISTS)
            ) {
                throw new \Parable\Console\Exception(
                    "Option '--{$option['name']}' requires a value, which is not provided."
                );
            }

            // Set default value if defaultValue is set and the option is either passed without value or not passed
            if (
                $option['defaultValue']
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
}
