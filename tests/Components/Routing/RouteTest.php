<?php

namespace Parable\Tests\Components\Routing;

class RouteTest extends \Parable\Tests\Base
{
    public function testSetDataFromArray()
    {
        $route = new \Parable\Routing\Route();
        $route->setDataFromArray([
            "methods" => ["GET"],
            "controller" => "stuff",
            "action" => "index"
        ]);
        $this->assertTrue($route->hasControllerAndAction());
        $this->assertSame(["GET"], $route->getMethods());
    }

    public function testSetDataFromArrayThrowsExceptionOnInvalidProperty()
    {
        $this->expectException(\Parable\Routing\Exception::class);
        $this->expectExceptionMessage("Tried to set non-existing property 'naww' with value 'index' on Route.");
        $route = new \Parable\Routing\Route();
        $route->setDataFromArray([
            "methods" => ["get"],
            "controller" => "stuff",
            "naww" => "index"
        ]);
    }

    public function testSetMethodsMakesAllUppercase()
    {
        $route = new \Parable\Routing\Route();
        $route->setMethods(["get", "pOsT", "OPTIoNS"]);

        $this->assertSame(["GET", "POST", "OPTIONS"], $route->getMethods());
    }

    public function testMethodIsAcceptedProperly()
    {
        $route = new \Parable\Routing\Route();
        $route->setMethods(["get"]);

        $_SERVER["REQUEST_METHOD"] = "POST";
        $this->assertFalse($route->isAcceptedRequestMethod());

        $_SERVER["REQUEST_METHOD"] = "GET";
        $this->assertTrue($route->isAcceptedRequestMethod());

        unset($_SERVER["REQUEST_METHOD"]);
    }
}
