<?php

namespace Parable\Tests;

class DiProxy
{
    public function get($className, $parentClassName = '')
    {
        return \Parable\DI\Container::get($className, $parentClassName);
    }

    public function create($className, $parentClassName = '')
    {
        return \Parable\DI\Container::create($className, $parentClassName);
    }

    public function store($instance, $name = null)
    {
        \Parable\DI\Container::store($instance, $name);
    }
}