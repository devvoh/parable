<?php

namespace Parable\Console\Parameter;

use Parable\Console\Exception;
use Parable\Console\Parameter;

class Argument extends Base
{
    /** @var int|null */
    protected $required;

    /** @var int|null */
    protected $order;

    /**
     * @param string     $name
     * @param int        $required
     * @param mixed|null $defaultValue
     */
    public function __construct(
        $name,
        $required = Parameter::PARAMETER_OPTIONAL,
        $defaultValue = null
    ) {
        $this->setName($name);
        $this->setRequired($required);
        $this->setDefaultValue($defaultValue);
    }

    /**
     * Set whether this argument is required.
     *
     * @param int $required
     *
     * @return $this
     * @throws Exception
     */
    public function setRequired($required)
    {
        if (!in_array(
            $required,
            [
                Parameter::PARAMETER_REQUIRED,
                Parameter::PARAMETER_OPTIONAL,
            ]
        )) {
            throw new Exception('Required must be one of the PARAMETER_* constants.');
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
        return $this->required === Parameter::PARAMETER_REQUIRED;
    }

    /**
     * Set the order for this argument.
     *
     * @param int $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = (int)$order;
        return $this;
    }

    /**
     * Return the order for this argument.
     *
     * @return int|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @inheritdoc
     */
    public function addParameters(array $parameters)
    {
        $this->setProvidedValue(null);
        $this->setHasBeenProvided(false);

        if (!array_key_exists($this->getOrder(), $parameters)) {
            return $this;
        }

        $this->setHasBeenProvided(true);
        $this->setProvidedValue($parameters[$this->getOrder()]);
        return $this;
    }
}
