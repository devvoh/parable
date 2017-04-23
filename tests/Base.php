<?php

namespace Parable\Tests;

abstract class Base extends \PHPUnit\Framework\TestCase
{
    /** @var \Parable\Tests\DiProxy */
    protected $diProxy;

    protected function setUp()
    {
        parent::setUp();

        $this->diProxy = new \Parable\Tests\DiProxy();
    }
}
