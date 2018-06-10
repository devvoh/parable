<?php

namespace Parable\Console\Parameter;

class Option extends Base
{
    /** @var int|null */
    protected $valueType;

    /** @var bool */
    protected $flagOption = false;

    /**
     * @param string     $name
     * @param int        $valueType
     * @param mixed|null $defaultValue
     * @param bool       $defaultValue
     */
    public function __construct(
        $name,
        $valueType = \Parable\Console\Parameter::OPTION_VALUE_OPTIONAL,
        $defaultValue = null,
        $flagOption = false
    ) {
        $this->setName($name);
        $this->setValueType($valueType);
        $this->setDefaultValue($defaultValue);
        $this->setFlagOption($flagOption);
    }

    /**
     * @param int $valueType
     *
     * @return $this
     * @throws \Parable\Console\Exception
     *
     * @deprecated Use setValueType instead
     *
     * @codeCoverageIgnore
     */
    public function setValueRequired($valueType)
    {
        return $this->setValueType($valueType);
    }

    /**
     * Set whether the option's value is required.
     *
     * @param int $valueType
     *
     * @return $this
     * @throws \Parable\Console\Exception
     */
    public function setValueType($valueType)
    {
        if (!in_array(
            $valueType,
            [
                \Parable\Console\Parameter::OPTION_VALUE_REQUIRED,
                \Parable\Console\Parameter::OPTION_VALUE_OPTIONAL,
            ]
        )) {
            throw new \Parable\Console\Exception('Value type must be one of the OPTION_* constants.');
        }

        $this->valueType = $valueType;
        return $this;
    }

    /**
     * Return whether the option's value is required.
     *
     * @return bool
     */
    public function isValueRequired()
    {
        return $this->valueType === \Parable\Console\Parameter::OPTION_VALUE_REQUIRED;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     * @throws \Parable\Console\Exception
     */
    public function setFlagOption($enabled)
    {
        if ($enabled && mb_strlen($this->getName()) > 1) {
            throw new \Parable\Console\Exception("Flag options can only have a single-letter name.");
        }
        $this->flagOption = (bool)$enabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFlagOption()
    {
        return $this->flagOption;
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
