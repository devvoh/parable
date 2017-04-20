<?php

namespace Parable\Tests\Classes;

class CyclicB
{
    public function __construct(\Parable\Tests\Classes\CyclicA $a)
    {
    }
}
