<?php

namespace Parable\Console\Parameter;

class Option extends Base
{
    /** @var int|null */
    protected $valueRequired;

    public function __construct(
        $name,
        $valueRequired = \Parable\Console\Parameter::OPTION_VALUE_OPTIONAL,
        $defaultValue = null
    ) {
        $this->setName($name);
        $this->setValueRequired($valueRequired);
        $this->setDefaultValue($defaultValue);
    }

    /**
     * Set whether the option's value is required.
     *
     * @param int $valueRequired
     *
     * @return $this
     * @throws \Parable\Console\Exception
     */
    public function setValueRequired($valueRequired)
    {
        if (!in_array(
            $valueRequired,
            [
                \Parable\Console\Parameter::OPTION_VALUE_REQUIRED,
                \Parable\Console\Parameter::OPTION_VALUE_OPTIONAL,
                \Parable\Console\Parameter::OPTION_FLAG,
            ]
        )) {
            throw new \Parable\Console\Exception('Value required must be one of the OPTION_* constants.');
        }
        $this->valueRequired = $valueRequired;
        return $this;
    }

    public function setIsFlag()
    {
        $this->valueRequired = \Parable\Console\Parameter::OPTION_FLAG;
        return $this;
    }

    /**
     * Return whether the option is a flag.
     *
     * @return bool
     */
    public function isFlag()
    {
        return $this->valueRequired === \Parable\Console\Parameter::OPTION_FLAG;
    }

    /**
     * Return whether the option's value is required.
     *
     * @return bool
     */
    public function isValueRequired()
    {
        return $this->valueRequired === \Parable\Console\Parameter::OPTION_VALUE_REQUIRED;
    }

    /**
     * @inheritdoc
     */
    public function addParameters(array $parameters)
    {
        $this->setProvidedValue(null);
        $this->setHasBeenProvided(false);

        if (!array_key_exists($this->getName(), $parameters)) {
            return $this;
        }

        $this->setHasBeenProvided(true);

        if ($parameters[$this->getName()] !== true) {
            $this->setProvidedValue($parameters[$this->getName()]);
        }

        return $this;
    }
}
