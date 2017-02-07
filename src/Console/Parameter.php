<?php

namespace Parable\Console;

class Parameter
{
    /** @var array */
    protected $rawArguments = [];

    /** @var string */
    protected $scriptName;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $arguments = [];

    public function __construct()
    {
        $this->rawArguments = $_SERVER["argv"];
        $this->parseArguments();
    }

    protected function parseArguments()
    {
        $this->scriptName = array_shift($this->rawArguments);

        $argumentsCopy = $this->rawArguments;
        foreach ($argumentsCopy as $key => $argument) {
            if (substr($argument, 0, 1) == '-') {
                $optionName = $this->trimDashes($argument);
                $this->options[$optionName] = $argumentsCopy[$key + 1];
                unset($argumentsCopy[$key + 1]);
            } else {
                $this->arguments[] = $argument;
            }
        }
    }

    protected function trimDashes($string)
    {
        return ltrim($string, '-');
    }

    public function getCommandName()
    {
        if (count($this->rawArguments) > 1) {
            return $this->rawArguments[0];
        }
    }
}
