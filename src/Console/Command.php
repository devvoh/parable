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
}
