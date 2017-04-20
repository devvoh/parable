<?php

namespace Parable\Tests\TestClasses;

class DependsOnBasic
{
    public $basic;

    public function __construct(\Parable\Tests\TestClasses\Basic $basic)
    {
        $this->basic = $basic;
    }
}
