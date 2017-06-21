<?php

namespace Controller;

class Home
{
    /**
     * @param \Parable\Routing\Route $route
     */
    public function index(\Parable\Routing\Route $route)
    {
    }

    /**
     * @param \Parable\Routing\Route $route
     * @param int                    $id
     * @param mixed                  $name
     */
    public function test(\Parable\Routing\Route $route, $id, $name)
    {
        /** @var \Parable\GetSet\Internal $internal */
        $internal = \Parable\DI\Container::get(\Parable\GetSet\Internal::class);
        $internal->set('id', $id);
        $internal->set('name', $name);
    }
}
