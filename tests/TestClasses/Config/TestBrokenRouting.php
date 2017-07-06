<?php

namespace Parable\Tests\TestClasses\Config;

class TestBrokenRouting implements
    \Parable\Framework\Interfaces\Config
{
    public function get()
    {
        return [
            "parable" => [
                "routes" => [
                    \Routing\Wrong::class,
                ],
            ],
        ];
    }
}
