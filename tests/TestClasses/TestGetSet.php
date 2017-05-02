<?php

namespace Parable\Tests\TestClasses;

class TestGetSet extends \Parable\Http\Values\GetSet
{
    /** @var string */
    protected $resource = 'test';

    /** @var bool */
    protected $useLocalResource = true;
}
