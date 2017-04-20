<?php

namespace Parable\Tests\Classes;

class DependsOnBasic
{
    public $basic;

    public function __construct(\Parable\Tests\Classes\Basic $basic)
    {
        $this->basic = $basic;
    }
}
