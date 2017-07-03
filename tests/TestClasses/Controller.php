<?php

namespace Parable\Tests\TestClasses;

class Controller
{
    public function index(\Parable\Routing\Route $route)
    {
    }

    public function simple(\Parable\Routing\Route $route)
    {
        return [$route];
    }

    public function complex(\Parable\Routing\Route $route, $id, $name)
    {
        return [$route, $id, $name];
    }
}
