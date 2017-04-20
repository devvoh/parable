<?php

namespace Parable\Tests\TestClasses;

class CyclicA
{
    public function __construct(\Parable\Tests\TestClasses\CyclicB $b)
    {
    }
}
