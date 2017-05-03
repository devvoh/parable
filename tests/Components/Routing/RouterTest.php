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
        $this->router->addRoute('callable', [
            'methods' => ['GET'],
            'url' => '/callable/{parameter}',
            'callable' => function (\Parable\Routing\Route $route, $parameter) {
                return [$route, $parameter];
            },
            'template' => 'test-file.phtml',
        ]);
    }

    protected function liberateRoute(\Parable\Routing\Route $route)
    {
        $this->liberateProperties($route, [
            'methods', 'url', 'controller', 'action', 'callable', 'template',
        ]);
    }

    public function testRouteAddedAndGetRouteByName()
    {
        $route = $this->router->getRouteByName('simple');
        $this->liberateRoute($route);

        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('/', $route->url);
        $this->assertSame(\Parable\Tests\TestClasses\Controller::class, $route->controller);
        $this->assertSame('simple', $route->action);

        $this->assertNull($route->callable);
        $this->assertNull($route->template);
    }

    public function testMatchCurrentRouteSimple()
    {
        $_GET['url'] = '/';

        $route = $this->router->matchCurrentRoute();

        $this->assertNotNull($route);

        $this->liberateRoute($route);

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

        $this->liberateRoute($route);

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

    public function testMatchCurrentRouteCallable()
    {
        $_GET['url'] = '/callable/stuff';

        $route = $this->router->matchCurrentRoute();

        $this->assertNotNull($route);

        $this->liberateRoute($route);

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
}
