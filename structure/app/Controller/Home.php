<?php

namespace Controller;

class Home
{
    /**
     * Index action
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
        /** @var \Parable\Http\Values\Internal $internal */
        $internal = \Parable\DI\Container::get(\Parable\Http\Values\Internal::class);
        $internal->set('id', $id);
        $internal->set('name', $name);
    }
}
