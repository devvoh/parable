<?php

namespace Parable\Console\Parameter;

abstract class Base
{
    /** @var string|null */
    protected $name;

    /** @var int|null */
    protected $required;

    /** @var mixed|null */
    protected $defaultValue;

    /** @var bool */
    protected $hasBeenProvided = false;

    /** @var string|null */
    protected $providedValue;

    /**
     * Set the name.
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
     * Return the name.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set whether this parameter is required.
     *
     * @param int $required
     *
     * @return $this
     * @throws \Parable\Console\Exception
     */
    public function setRequired($required)
    {
        if (!in_array(
            $required,
            [\Parable\Console\Parameter::PARAMETER_REQUIRED, \Parable\Console\Parameter::PARAMETER_OPTIONAL])
        ) {
            throw new \Parable\Console\Exception("Required must one of the PARAMETER_* constants.");
        }
        $this->required = $required;
        return $this;
    }

    /**
     * Return whether the parameter is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required === \Parable\Console\Parameter::PARAMETER_REQUIRED;
    }

    /**
     * Set the default value.
     *
     * @param mixed $defaultValue
     *
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Return the default value.
     *
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set whether the parameter has been provided.
     *
     * @param bool $hasBeenProvided
     *
     * @return $this
     */
    public function setHasBeenProvided($hasBeenProvided)
    {
        $this->hasBeenProvided = (bool)$hasBeenProvided;
        return $this;
    }

    /**
     * Return whether the parameter has been provided.
     *
     * @return bool
     */
    public function hasBeenProvided()
    {
        return $this->hasBeenProvided;
    }

    /**
     * Set the value that was provided.
     *
     * @param string $providedValue
     *
     * @return $this
     */
    public function setProvidedValue($providedValue)
    {
        $this->providedValue = $providedValue;
        return $this;
    }

    /**
     * Return the provided value.
     *
     * @return null|string
     */
    public function getProvidedValue()
    {
        return $this->providedValue;
    }

    /**
     * Get the value. The provided value if available, otherwise the default.
     *
     * @return mixed|null|string
     */
    public function getValue()
    {
        if ($this->getProvidedValue()) {
            return $this->getProvidedValue();
        }

        return $this->getDefaultValue();
    }

    /**
     * Add data from the parameter arguments to decide whether this parameter type
     * has been provided and set the provided value, if any.
     *
     * @param array $parameters
     *
     * @return $this
     */
    abstract public function addParameters(array $parameters);
}