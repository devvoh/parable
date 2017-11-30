<?php

namespace Parable\Console\Parameter;

class Argument extends Base
{
    /** @var int|null */
    protected $order;

    public function __construct(
        $name,
        $required = \Parable\Console\Parameter::PARAMETER_OPTIONAL,
        $defaultValue = null
    ) {
        $this->setName($name);
        $this->setRequired($required);
        $this->setDefaultValue($defaultValue);
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