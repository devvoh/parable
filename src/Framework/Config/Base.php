<?php

namespace Parable\Framework\Config;

abstract class Base
{
    /** @var null|int */
    protected $sortOrder;

    /**
     * Defines what order the class is loaded in, allowing overwriting values in a specific order.
     * If $sortOrder is null, the class will be loaded at the end of the process. So a $sortOrder = null class will
     * always override settings of classes with a $sortOrder set to an integer.
     *
     * @return null|int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Returns a (multidimensional) array with configuration values
     *
     * @return array
     */
    abstract public function getValues();
}
