<?php

namespace Parable\Framework\Interfaces;

interface Config
{
    /**
     * Defines what order the class is loaded in, allowing overwriting values in a specific order.
     * If $sortOrder is null, the class will be loaded at the end of the process. So a $sortOrder = null class will
     * always override settings of classes with a $sortOrder set to an integer.
     *
     * @return null|int
     */
    public function getSortOrder();

    /**
     * Returns a (multidimensional) array with configuration values
     *
     * @return array
     */
    public function getValues();
}
