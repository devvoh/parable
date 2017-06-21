<?php

namespace Parable\Tests\TestClasses;

class TestGetSet extends \Parable\GetSet\Base
{
    /** @var string */
    protected $resource = 'test';

    /** @var bool */
    protected $useLocalResource = true;
}
