<?php

namespace Parable\Tests\Classes;

class CyclicA
{
    public function __construct(\Parable\Tests\Classes\CyclicB $b)
    {
    }
}
