<?php

namespace Parable\Tests\TestClasses\Config;

class Custom implements
    \Parable\Framework\Interfaces\Config
{
    public function get()
    {
        return [
            "key" => "value",
        ];
    }
}
