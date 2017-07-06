<?php

namespace Routing;

class Wrong implements
    \Parable\Framework\Interfaces\Gettable
{
    public function get()
    {
        return [];
    }
}
