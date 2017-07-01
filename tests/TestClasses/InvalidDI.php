<?php

namespace Parable\Tests\TestClasses;

class InvalidDI
{
    public function __construct(
        \NonExisting\Thing $thing
    ) {
    }
}
