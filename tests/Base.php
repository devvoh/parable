<?php

namespace Parable\Tests;

abstract class Base extends \PHPUnit\Framework\TestCase
{
    /** @var \Parable\Tests\Di */
    protected $di;

    protected function setUp()
    {
        parent::setUp();

        $this->di = new \Parable\Tests\Di();
        $this->getActualOutput();
    }
}
