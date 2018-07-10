<?php

namespace Parable\Console;

use Parable\Console\Parameter\Argument;
use Parable\Console\Parameter\Option;

class Parameter
{
    const PARAMETER_REQUIRED    = 1;
    const PARAMETER_OPTIONAL    = 2;

    const OPTION_VALUE_REQUIRED = 11;
    const OPTION_VALUE_OPTIONAL = 12;

    /** @var array */
    protected $parameters = [];

    /** @var string|null */
    protected $scriptName;

    /** @var string|null */
    protected $commandName;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $flagOptions = [];

    /** @var array */
    protected $arguments = [];

    /** @var Option[] */
    protected $commandOptions = [];

    /** @var Argument[] */
    protected $commandArguments = [];

    /** @var bool */
    protected $commandNameEnabled = true;

    public function __construct()
    {
        $this->setParameters($_SERVER["argv"]);
    }

    /**
     * Set parameters and parse them.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        $this->parseParameters();
        return $this;
    }

    /**
     * Return the currently set parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Split the parameters into script name, command name, options and arguments.
     *
     * Flag options can be passed in a single set preceded by a dash:
     *   -a -b -c
     * or concatenated together, which looks like this:
     *   -abc
     *
     * When an option is encountered with a value set, everything after = is seen as that value:
     *   -a -b -c=def
     * or:
     *   -abc=def
     *
     * @return $this
     */
    public function parseParameters()
    {
        $this->reset();

        // Extract the scriptName
        $this->scriptName = array_shift($this->parameters);

        foreach ($this->parameters as $parameter) {
            $optionString = ltrim($parameter, '-');

            if (substr($parameter, 0, 2) === "--") {
                $this->parseOption($optionString);
            } elseif (substr($parameter, 0, 1) === "-") {
                $this->parseFlagOption($optionString);
            } else {
                $this->parseArgument($parameter);
            }
        }
        return $this;
    }

    /**
     * Parse a long option (--option) string.
     *
     * @param string $optionString
     *
     * @return $this
     */
    protected function parseOption($optionString)
    {
        $optionParts = explode('=', $optionString);

        if (count($optionParts) > 1) {
            list($key, $value) = $optionParts;
        } else {
            $key   = $optionString;
            $value = true;
        }

        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Parse a flag option string (-a or -abc, this last version
     * is parsed as a concatenated string of one char per option).
     *
     * @param string $optionString
     *
     * @return $this
     */
    protected function parseFlagOption($optionString)
    {
        for ($i = 0; $i < strlen($optionString); $i++) {
            $optionChar = substr($optionString, $i, 1);
            $optionParts = explode('=', substr($optionString, $i + 1));

            if (count($optionParts) > 1 && empty($optionParts[0])) {
                $value = $optionParts[1];
            } elseif ($optionChar !== "=") {
                $value = true;
            } else {
                break;
            }

            $this->flagOptions[$optionChar] = $value;
        }
        return $this;
    }

    /**
     * Parse argument. If no command name set and commands are enabled,
     * interpret as command name. Otherwise, add to argument list.
     *
     * @param string $parameter
     *
     * @return $this
     */
    protected function parseArgument($parameter)
    {
        if ($this->commandNameEnabled && !$this->commandName) {
            $this->commandName = $parameter;
        } else {
            $this->arguments[] = $parameter;
        }
        return $this;
    }

    /**
     * Return the script name.
     *
     * @return string
     */
    public function getScriptName()
    {
        return $this->scriptName;
    }

    /**
     * Return the command name.
     *
     * @return null|string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * Set the options from a command.
     *
     * @param Option[] $options
     *
     * @return $this
     * @throws Exception
     */
    public function setCommandOptions(array $options)
    {
        foreach ($options as $name => $option) {
            if ((!$option instanceof Option)) {
                throw new Exception(
                    "Options must be instances of Parameter\\Option. {$name} is not."
                );
            }
            $this->commandOptions[$option->getName()] = $option;
        }
        return $this;
    }

    /**
     * Checks the options set against the parameters set. Takes into account whether an option is required
     * to be passed or not, or a value is required if it's passed, or sets the defaultValue if given and necessary.
     *
     * @throws Exception
     */
    public function checkCommandOptions()
    {
        foreach ($this->commandOptions as $option) {
            if ($option->isFlagOption()) {
                $parameters = $this->flagOptions;
            } else {
                $parameters = $this->options;
            }
            $option->addParameters($parameters);

            if ($option->isValueRequired() && $option->hasBeenProvided() && !$option->getValue()) {
                if ($option->isFlagOption()) {
                    $message = "Option '-{$option->getName()}' requires a value, which is not provided.";
                } else {
                    $message = "Option '--{$option->getName()}' requires a value, which is not provided.";
                }
                throw new Exception($message);
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
        if (!array_key_exists($name, $this->commandOptions)) {
            return null;
        }

        $option = $this->commandOptions[$name];

        if ($option->hasBeenProvided() && $option->getProvidedValue() === null && $option->getDefaultValue() === null) {
            return true;
        }

        return $option->getValue();
    }

    /**
     * Return all option values.
     *
     * @return array
     */
    public function getOptions()
    {
        $returnArray = [];
        foreach ($this->commandOptions as $option) {
            $returnArray[$option->getName()] = $this->getOption($option->getName());
        }
        return $returnArray;
    }

    /**
     * Set the arguments from a command.
     *
     * @param Argument[] $arguments
     *
     * @return $this
     * @throws Exception
     */
    public function setCommandArguments(array $arguments)
    {
        $orderedArguments = [];
        foreach ($arguments as $index => $argument) {
            if (!($argument instanceof Argument)) {
                throw new Exception(
                    "Arguments must be instances of Parameter\\Argument. The item at index {$index} is not."
                );
            }

            $argument->setOrder($index);
            $orderedArguments[$index] = $argument;
        }
        $this->commandArguments = $orderedArguments;

        return $this;
    }

    /**
     * Checks the arguments set against the parameters set. Takes into account whether an argument is required
     * to be passed or not.
     *
     * @throws Exception
     */
    public function checkCommandArguments()
    {
        foreach ($this->commandArguments as $index => $argument) {
            $argument->addParameters($this->arguments);

            if ($argument->isRequired() && !$argument->hasBeenProvided()) {
                throw new Exception(
                    "Required argument with index #{$index} '{$argument->getName()}' not provided."
                );
            }
        }
    }

    /**
     * Returns null if the value doesn't exist. Returns default value if set from command, and the actual value
     * if passed on the command line.
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function getArgument($name)
    {
        foreach ($this->commandArguments as $argument) {
            if ($argument->getName() === $name) {
                return $argument->getValue();
            }
        }
        return null;
    }

    /**
     * Return all arguments passed.
     *
     * @return array
     */
    public function getArguments()
    {
        $returnArray = [];
        foreach ($this->commandArguments as $argument) {
            $returnArray[$argument->getName()] = $argument->getValue();
        }
        return $returnArray;
    }

    /**
     * Reset the class to a fresh state.
     *
     * @return $this
     */
    protected function reset()
    {
        $this->scriptName      = null;
        $this->commandName     = null;
        $this->options         = [];
        $this->arguments       = [];

        return $this;
    }

    /**
     * Remove the command name from the arguments, if a command name is actually set.
     *
     * @return $this;
     */
    public function enableCommandName()
    {
        if (!$this->commandNameEnabled
            && $this->commandName
            && isset($this->arguments[0])
            && $this->arguments[0] === $this->commandName
        ) {
            unset($this->arguments[0]);
            $this->arguments = array_values($this->arguments);
        }
        $this->commandNameEnabled = true;
        return $this;
    }

    /**
     * Add the command name to the arguments, if a command name is set.
     *
     * @return $this;
     */
    public function disableCommandName()
    {
        if ($this->commandNameEnabled && $this->commandName) {
            array_unshift($this->arguments, $this->commandName);
        }
        $this->commandNameEnabled = false;
        return $this;
    }
}
