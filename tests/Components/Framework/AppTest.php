<?php

namespace Parable\Tests\Components\Framework;

class AppTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\App */
    protected $app;

    /** @var array */
    protected $config = [];

    /** @var \Parable\Routing\Router */
    protected $router;

    /** @var \Parable\Http\Response|\PHPUnit_Framework_MockObject_MockObject */
    protected $mockResponse;

    /** @var \Parable\GetSet\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $mockSession;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Parable\Http\Response $this->mockResponse */
        // Response should not actually terminate
        $this->mockResponse = $this->createPartialMock(\Parable\Http\Response::class, ['terminate']);
        $this->mockResponse->__construct(\Parable\DI\Container::get(\Parable\Http\Request::class));

        \Parable\DI\Container::store($this->mockResponse, \Parable\Http\Response::class);

        /** @var \Parable\GetSet\Session $this->mockSession */
        // Fake the session since we need to take out the starting of the session
        $this->mockSession = $this->createPartialMock(\Parable\GetSet\Session::class, ['start']);
        $this->mockSession->expects($this->any())->method('start')->willReturn($this->mockSession);

        \Parable\DI\Container::store($this->mockSession, \Parable\GetSet\Session::class);

        $this->router = \Parable\DI\Container::get(\Parable\Routing\Router::class);
        $this->router->addRoute('index', [
            'methods' => ['GET'],
            'url' => '/',
            'controller' => \Parable\Tests\TestClasses\Controller::class,
            'action' => 'index',
            'template' => $this->path->getDir('tests/TestTemplates/index.phtml'),
        ]);

        $this->app = $this->createApp();
    }

    public function testAppRun()
    {
        $this->mockResponse->expects($this->once())->method('terminate');
        $this->app->run();

        $output = $this->getActualOutputAndClean();

        $this->assertContains('PHP framework by <a href="http://devvoh.com">devvoh</a>', $output);
        $this->assertSame(200, $this->mockResponse->getHttpCode());
        $this->assertSame('OK', $this->mockResponse->getHttpCodeText());
    }

    public function testAppRunWithoutRoutesTriggersHookNoRoutesFound()
    {
        $hook = \Parable\DI\Container::get(\Parable\Event\Hook::class);
        $hook->into(\Parable\Framework\App::HOOK_LOAD_ROUTES_NO_ROUTES_FOUND, function($event) {
            $this->assertSame(\Parable\Framework\App::HOOK_LOAD_ROUTES_NO_ROUTES_FOUND, $event);
        });

        $app = $this->createApp(\Parable\Tests\TestClasses\Config\TestNoRouting::class);
        $app->run();

        $this->getActualOutputAndClean();
    }

    public function testAppRunWithUnknownUrlGives404()
    {
        $_GET['url'] = '/simple';

        $this->app->run();

        $this->getActualOutputAndClean();

        $this->assertSame(404, $this->mockResponse->getHttpCode());
        $this->assertSame("Not Found", $this->mockResponse->getHttpCodeText());
    }

    public function testAppRunWithSimpleUrlWorks()
    {
        $_GET['url'] = '/simple';
        $this->router->addRoute(
            'simple',
            [
                'methods' => ['GET'],
                'url' => '/simple',
                'callable' => function (\Parable\Routing\Route $route) {
                    echo "callable route found";
                }
            ]
        );

        $this->app->run();

        $output = $this->getActualOutputAndClean();

        $this->assertSame('callable route found', $output);
    }

    public function testAppRunWithTemplatedUrlWorks()
    {
        $path = \Parable\DI\Container::get(\Parable\Filesystem\Path::class);

        $_GET['url'] = '/template';
        $this->router->addRoute(
            'template',
            [
                'methods' => ['GET'],
                'url' => '/template',
                'callable' => function (\Parable\Routing\Route $route) {
                    echo "Hello";
                },
                'template' => $path->getDir('tests/TestTemplates/app_test_template.phtml'),
            ]
        );

        $this->app->run();

        $output = $this->getActualOutputAndClean();

        $this->assertSame('Hello, world!', $output);
    }

    public function testLoadRoutesThrowsExceptionWhenInvalidRouteIsAdded()
    {
        $this->expectException(\Parable\Routing\Exception::class);
        $this->expectExceptionMessage("Either a controller/action combination or callable is required.");

        $this->router->addRoute(
            'simple',
            [
                'methods' => ['GET'],
                'url' => '/simple',
            ]
        );

        $this->app->run();
    }

    public function testAppRunWithValuedUrlWorks()
    {
        $_GET['url'] = '/valued/985';
        $this->router->addRoute(
            'valued',
            [
                'methods' => ['GET'],
                'url' => '/valued/{id}',
                'callable' => function (\Parable\Routing\Route $route, $id) {
                    echo "callable route found with id: {$id}";
                }
            ]
        );

        $this->app->run();

        $output = $this->getActualOutputAndClean();

        $this->assertSame('callable route found with id: 985', $output);
    }

    public function testAppThrowsExceptionOnInvalidRoute()
    {
        $this->expectException(\Parable\Routing\Exception::class);
        $this->expectExceptionMessage("Either a controller/action combination or callable is required.");

        $this->router->addRoute('simple', [
            'methods' => ['GET'],
            'url' => '/',
        ]);

        $app = $this->createApp();
        $app->run();

        // And reset
        \Parable\DI\Container::clear(\Routing\App::class);
    }

    public function testAppThrowsExceptionOnWrongRouteInterface()
    {
        $this->expectException(\Parable\Framework\Exception::class);
        $this->expectExceptionMessage("Routing\Wrong does not implement \Parable\Framework\Interfaces\Routing");

        $app = $this->createApp(\Parable\Tests\TestClasses\Config\TestBrokenRouting::class);
        $app->run();
    }

    /**
     * @param string $mainConfigClassName
     * @return \Parable\Framework\App
     */
    protected function createApp($mainConfigClassName = \Parable\Tests\TestClasses\Config\Test::class)
    {
        /** @var \Parable\Framework\Config|\PHPUnit_Framework_MockObject_MockObject $config */
        $config = new \Parable\Framework\Config($this->path);
        $config->setMainConfigClassName($mainConfigClassName);
        $config->load();

        \Parable\DI\Container::store($config);

        return \Parable\DI\Container::create(\Parable\Framework\App::class);
    }
}
