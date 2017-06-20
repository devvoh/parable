<?php

namespace Parable\Tests\Components\DI;

class ContainerTest extends \Parable\Tests\Base
{
    /** @var \stdClass */
    protected $object;

    public function testCreateNewInstance()
    {
        $diObject = \Parable\DI\Container::create(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("new", $diObject->value);
    }

    public function testDIDoesNotCareAboutPrefixedOrUnprefixedClassNames()
    {
        $diObject1 = \Parable\DI\Container::get("Parable\Tests\TestClasses\Basic");
        $diObject2 = \Parable\DI\Container::get("\Parable\Tests\TestClasses\Basic");
        $this->assertSame($diObject1, $diObject2);
    }

    public function testGetInstanceReturnsSame()
    {
        $diObjectOriginal = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("new", $diObjectOriginal->value);

        $diObjectOriginal->value = "updated";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("updated", $diObject->value);
    }

    public function testCreateAlwaysGivesNewInstance()
    {
        $diObjectOriginal = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $diObjectOriginal->value = "updated";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("updated", $diObject->value);

        $diObject = \Parable\DI\Container::create(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("new", $diObject->value);
    }

    public function testCreateAllAlwaysGivesAllNewInstances()
    {
        $diObjectOriginal = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $diObjectOriginal->value = "updated";

        $diObject = \Parable\DI\Container::create(\Parable\Tests\TestClasses\DependsOnBasic::class);
        $this->assertSame("updated", $diObject->basic->value);

        $diObject = \Parable\DI\Container::createAll(\Parable\Tests\TestClasses\DependsOnBasic::class);
        $this->assertSame("new", $diObject->basic->value);
    }

    public function testDependenciesResolvedProperly()
    {
        $diObject = \Parable\DI\Container::create(\Parable\Tests\TestClasses\DependsOnBasic::class);
        $this->assertNotNull($diObject->basic);
        $this->assertInstanceOf(\Parable\Tests\TestClasses\Basic::class, $diObject->basic);
    }

    public function testStoreInstanceIsReturnedProperly()
    {
        $diObjectOriginal = new \Parable\Tests\TestClasses\Basic();
        \Parable\DI\Container::store($diObjectOriginal);

        $this->assertSame("new", $diObjectOriginal->value);

        $diObjectOriginal->value = "stored";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("stored", $diObject->value);
    }

    public function testStoreInstanceIsReturnedProperlyUntilCleared()
    {
        $diObjectOriginal = new \Parable\Tests\TestClasses\Basic();
        \Parable\DI\Container::store($diObjectOriginal);

        $this->assertSame("new", $diObjectOriginal->value);

        $diObjectOriginal->value = "stored";

        $diObject = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("stored", $diObject->value);

        \Parable\DI\Container::clear(\Parable\Tests\TestClasses\Basic::class);

        $diObject = \Parable\DI\Container::get(\Parable\Tests\TestClasses\Basic::class);
        $this->assertSame("new", $diObject->value);
    }

    public function testExceptionOnInvalidClass()
    {
        $this->expectException(\Parable\DI\Exception::class);
        $this->expectExceptionMessage("Could not create instance of 'This class does not exist'");

        \Parable\DI\Container::create("This class does not exist");
    }

    public function testExceptionOnInvalidClassAsADependency()
    {
        $this->expectException(\Parable\DI\Exception::class);
        $this->expectExceptionMessage(
            "Could not create instance of 'thing', required by 'Parable\Tests\TestClasses\InvalidDI'"
        );

        \Parable\DI\Container::create(\Parable\Tests\TestClasses\InvalidDI::class);
    }

    public function testExceptionOnCyclicalDependency()
    {
        $this->expectException(\Parable\DI\Exception::class);
        $this->expectExceptionMessage(
            "Cyclical dependency found: Parable\\Tests\\TestClasses\\CyclicA depends on Parable\\Tests\\TestClasses\\CyclicB but is itself a dependency of Parable\\Tests\\TestClasses\\CyclicB."
        );
        \Parable\DI\Container::create(\Parable\Tests\TestClasses\CyclicA::class);
    }

    public function testClearAllActuallyClearsAll()
    {
        // Path is the only thing stored in DI that needs to remain, so let's use that.
        $path_original = clone \Parable\DI\Container::get(\Parable\Filesystem\Path::class);

        $path_get      = \Parable\DI\Container::get(\Parable\Filesystem\Path::class);
        $path_create   = \Parable\DI\Container::create(\Parable\Filesystem\Path::class);

        $this->assertSame($path_original->getBaseDir(), $path_get->getBaseDir());
        $this->assertNotSame($path_original->getBaseDir(), $path_create->getBaseDir());

        \Parable\DI\Container::clearAll();

        $path_get      = \Parable\DI\Container::get(\Parable\Filesystem\Path::class);
        $path_create   = \Parable\DI\Container::create(\Parable\Filesystem\Path::class);

        $this->assertSame($path_get->getBaseDir(), $path_create->getBaseDir());
        $this->assertNotSame($path_original->getBaseDir(), $path_get->getBaseDir());

        // And now that we've tested it, we need to put Path back for the remainder of the tests
        \Parable\DI\Container::store($path_original);
    }
}
