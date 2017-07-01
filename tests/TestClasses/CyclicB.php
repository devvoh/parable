<?php

namespace Parable\Tests\TestClasses;

class CyclicB
{
    public function __construct(\Parable\Tests\TestClasses\CyclicA $a)
    {
    }
}
