<?php

namespace Parable\Tests\Components\Routing;

class RouterTest extends \Parable\Tests\Base
{
    /** @var \Parable\Routing\Router */
    protected $router;

    protected function setUp()
    {
        parent::setUp();

        // Since the Router depends on \Parable\Http\Request, which depends on some $_SERVER values, we set them
        $GLOBALS['_SERVER']['REQUEST_METHOD'] = "GET";

        $this->router = \Parable\DI\Container::get(\Parable\Routing\Router::class);

        $this->router->addRoute('simple', [
            'methods' => ['GET'],
            'url' => '/',
            'controller' => \Parable\Tests\TestClasses\Controller::class,
            'action' => 'simple',
        ]);
        $this->router->addRoute('complex', [
            'methods' => ['GET'],
            'url' => '/complex/{id}/{name}',
            'controller' => \Parable\Tests\TestClasses\Controller::class,
            'action' => 'complex',
        ]);
        $this->router->addRoute('complextyped', [
            'methods' => ['GET'],
            'url' => '/complextyped/{id:int}/{float:float}',
            'controller' => \Parable\Tests\TestClasses\Controller::class,
            'action' => 'complextyped',
        ]);
        $this->router->addRoute('callable', [
            'methods' => ['GET'],
            'url' => '/callable/{parameter}',
            'callable' => function (\Parable\Routing\Route $route, $parameter) {
                return [$route, $parameter];
            },
            'template' => 'test-file.phtml',
        ]);
    }

    public function testInvalidRouteThrowsException()
    {
        $this->expectExceptionMessage("Either a controller/action combination or callable is required.");
        $this->expectException(\Parable\Routing\Exception::class);

        $this->router->addRoute('invalid', []);
    }

    public function testAddRouteNoMethodsThrowsException()
    {
        $this->expectExceptionMessage("Methods are required and must be passed as an array.");
        $this->expectException(\Parable\Routing\Exception::class);

        $this->router->addRoute('invalid', ['callable' => 'test']);
    }

    public function testAddRouteInvalidMethodsThrowsException()
    {
        $this->expectExceptionMessage("Methods are required and must be passed as an array.");
        $this->expectException(\Parable\Routing\Exception::class);

        $this->router->addRoute('invalid', ['methods' => 'GET', 'callable' => 'test']);
    }

    public function testAddRouteInvalidMethodsAcceptedReturnsNull()
    {
        $_GET['url'] = '/easy';
        $this->router->addRoute('callable', [
            'methods' => ['GET'],
            'url' => '/easy',
            'callable' => function () {
            },
        ]);
        $this->assertInstanceOf(\Parable\Routing\Route::class, $this->router->matchRoute('/easy'));

        // Now re-add as a POST-only route
        $this->router->addRoute('callable', [
            'methods' => ['POST'],
            'url' => '/easy',
            'callable' => function () {
            },
        ]);
        $this->assertNull($this->router->matchRoute('/easy'));
    }

    public function testRouteAddedAndGetRouteByName()
    {
        $route = $this->router->getRouteByName('simple');

        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('/', $route->url);
        $this->assertSame(\Parable\Tests\TestClasses\Controller::class, $route->controller);
        $this->assertSame('simple', $route->action);

        $this->assertNull($route->callable);
        $this->assertNull($route->template);
    }

    public function testInvalidGetRouteByNameReturnsNull()
    {
        $this->assertNull($this->router->getRouteByName('la-dee-dah'));
    }

    public function testMatchCurrentRouteSimple()
    {
        $_GET['url'] = '/';

        $route = $this->router->matchCurrentRoute();

        $this->assertNotNull($route);

        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('/', $route->url);
        $this->assertSame(\Parable\Tests\TestClasses\Controller::class, $route->controller);
        $this->assertSame('simple', $route->action);

        $this->assertNull($route->callable);
        $this->assertNull($route->template);

        $this->assertFalse($route->hasParameters());
    }

    public function testMatchCurrentRouteComplex()
    {
        $_GET['url'] = '/complex/id-value/name-value';

        $route = $this->router->matchCurrentRoute();

        $this->assertNotNull($route);

        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('/complex/{id}/{name}', $route->url);
        $this->assertSame(\Parable\Tests\TestClasses\Controller::class, $route->controller);
        $this->assertSame('complex', $route->action);

        $this->assertNull($route->callable);
        $this->assertNull($route->template);

        $this->assertTrue($route->hasParameters());

        $this->assertSame(
            [
                'id'   => 'id-value',
                'name' => 'name-value',
            ],
            $route->getValues()
        );
    }

    public function testMatchCurrentRouteComplexTyped()
    {
        $_GET['url'] = '/complextyped/1/1.45';

        $route = $this->router->matchCurrentRoute();

        $this->assertNotNull($route);

        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('/complextyped/{id:int}/{float:float}', $route->url);
        $this->assertSame(\Parable\Tests\TestClasses\Controller::class, $route->controller);
        $this->assertSame('complextyped', $route->action);

        $this->assertNull($route->callable);
        $this->assertNull($route->template);

        $this->assertTrue($route->hasParameters());

        $this->assertSame(
            [
                'id'    => 1,
                'float' => 1.45,
            ],
            $route->getValues()
        );
    }

    public function testMatchCurrentRouteCallable()
    {
        $_GET['url'] = '/callable/stuff';

        $route = $this->router->matchCurrentRoute();

        $this->assertNotNull($route);

        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('/callable/{parameter}', $route->url);
        $this->assertSame('test-file.phtml', $route->template);
        $this->assertSame(
            [
                'parameter' => 'stuff',
            ],
            $route->getValues()
        );

        $this->assertNotNull($route->callable);

        $callable = $route->callable;

        $parameters = [$route];
        foreach ($route->getValues() as $value) {
            $parameters[] = $value;
        }

        $values = $callable(...$parameters);

        $this->assertSame('stuff', $values[1]);
    }

    public function testParameterTypesMatterForInts()
    {
        $this->router->addRoute('callable', [
            'methods' => ['GET'],
            'url' => '/int/{int:int}',
            'callable' => function () {
            },
        ]);

        // With an int value we're fine
        $_GET['url'] = '/int/1';
        $route = $this->router->matchCurrentRoute();

        $this->assertSame(1, $route->getValue('int'));

        // But with a string value it should fail
        $_GET['url'] = '/int/string';
        $route = $this->router->matchCurrentRoute();

        $this->assertNull($route);

        // Same for float
        $_GET['url'] = '/int/1.45';
        $route = $this->router->matchCurrentRoute();

        $this->assertNull($route);
    }

    public function testParameterTypesMatterForFloats()
    {
        $this->router->addRoute('callable', [
            'methods' => ['GET'],
            'url' => '/float/{float:float}',
            'callable' => function () {
            },
        ]);

        // With an float value we're fine
        $_GET['url'] = '/float/1.23';
        $route = $this->router->matchCurrentRoute();

        $this->assertSame(1.23, $route->getValue('float'));

        // With an int value we're fine, because it CAN be represented as a float
        $_GET['url'] = '/float/1';
        $route = $this->router->matchCurrentRoute();

        $this->assertSame(1.0, $route->getValue('float'));

        // But with a string value it should fail
        $_GET['url'] = '/float/string';
        $route = $this->router->matchCurrentRoute();

        $this->assertNull($route);
    }

    public function testMatchNonExistingRoute()
    {
        $_GET['url'] = '/non-existent';
        $this->assertNull($this->router->matchCurrentRoute());
    }

    public function testGetRouteUrlByName()
    {
        $route = $this->router->getRouteUrlByName('complex');
        $this->assertSame("/complex/{id}/{name}", $route);
    }

    public function testGetRouteUrlByNameWithParameters()
    {
        $route = $this->router->getRouteUrlByName('complex', ['id' => 2, 'name' => 'stuff']);
        $this->assertSame("/complex/2/stuff", $route);
    }

    public function testGetRouteUrlByNameNonExistingRoute()
    {
        $this->assertNull($this->router->getRouteUrlByName('non-existent'));
    }

    public function testRouteReturnsNullOnNonExistingValueKey()
    {
        $_GET['url'] = '/';
        $route = $this->router->matchCurrentRoute();

        $this->assertInstanceOf(\Parable\Routing\Route::class, $route);

        $this->assertNull($route->getValue('stuff'));
    }

    public function testRouteBuildUrlWithoutParameters()
    {
        $_GET['url'] = '/';
        $route = $this->router->matchCurrentRoute();

        $this->assertInstanceOf(\Parable\Routing\Route::class, $route);

        $this->assertSame('/', $route->buildUrlWithParameters([]));
    }
}
