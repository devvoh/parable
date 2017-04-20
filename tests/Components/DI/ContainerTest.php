<?php

namespace Parable\Tests\Components\DI;

class ContainerTest extends \Parable\Tests\Base
{
    /** @var \stdClass */
    protected $object;

    public function testCreateNewInstance()
    {
        $diObject = \Parable\DI\Container::create(\Parable\Tests\Classes\Basic::class);
        $this->assertSame("new", $diObject->value);
    }

    public function testGetInstanceReturnsSame()
    {
        $diObjectOriginal = \Parable\DI\Container::get(\Parable\Tests\Classes\Basic::class);
        $this->assertSame("new", $diObjectOriginal->value);

        $diObjectOriginal->value = "updated";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\Classes\Basic::class);
        $this->assertSame("updated", $diObject->value);
    }

    public function testCreateAlwaysGivesNewInstance()
    {
        $diObjectOriginal = \Parable\DI\Container::get(\Parable\Tests\Classes\Basic::class);
        $diObjectOriginal->value = "updated";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\Classes\Basic::class);
        $this->assertSame("updated", $diObject->value);

        $diObject = \Parable\DI\Container::create(\Parable\Tests\Classes\Basic::class);
        $this->assertSame("new", $diObject->value);
    }

    public function testDependenciesResolvedProperly()
    {
        $diObject = \Parable\DI\Container::create(\Parable\Tests\Classes\DependsOnBasic::class);
        $this->assertNotNull($diObject->basic);
    }

    public function testStoreInstanceIsReturnedProperly()
    {
        $diObjectOriginal = new \Parable\Tests\Classes\Basic();
        \Parable\DI\Container::store($diObjectOriginal);

        $this->assertSame("new", $diObjectOriginal->value);

        $diObjectOriginal->value = "stored";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\Classes\Basic::class);
        $this->assertSame("stored", $diObject->value);
    }

    public function testExceptionOnInvalidClass()
    {
        $this->expectException(\Parable\DI\Exception::class);
        $this->expectExceptionMessage("Could not create instance of 'This test no existy'");

        \Parable\DI\Container::create("This test no existy");
    }

    public function testExceptionOnCyclicalDependency()
    {
        $this->expectException(\Parable\DI\Exception::class);
        $this->expectExceptionMessage(
            "Cyclical dependency found: Parable\\Tests\\Classes\\CyclicA depends on Parable\\Tests\\Classes\\CyclicB but is itself a dependency of Parable\\Tests\\Classes\\CyclicB."
        );
        \Parable\DI\Container::create(\Parable\Tests\Classes\CyclicA::class);
    }
}
