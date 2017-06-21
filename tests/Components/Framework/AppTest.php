<?php

namespace Parable\Tests\Components\Framework;

class AppTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\App */
    protected $app;

    /** @var array */
    protected $config = [];

    /** @var \Parable\Routing\Router */
    protected $mockRouter;

    /** @var \Parable\Http\Response|\PHPUnit_Framework_MockObject_MockObject */
    protected $mockResponse;

    /** @var \Parable\Filesystem\Path */
    protected $mockPath;

    /** @var \Parable\Framework\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $mockConfig;

    /** @var \Parable\Framework\Dispatcher */
    protected $mockDispatcher;

    protected function setUp()
    {
        parent::setUp();

        // We need to 'prepare' some classes since they'll expect files elsewhere
        $this->mockPath = new \Parable\Filesystem\Path();
        $existingPath = \Parable\DI\Container::get(\Parable\Filesystem\Path::class);
        $this->mockPath->setBasedir(realpath($existingPath->getBaseDir() . '/structure'));

        /** @var \Parable\Http\Response $response */
        // Response should not actually terminate
        $this->mockResponse = $this->createPartialMock(\Parable\Http\Response::class, ['terminate']);
        $this->mockResponse->__construct();

        \Parable\DI\Container::store($this->mockResponse, \Parable\Http\Response::class);

        $this->mockDispatcher = new \Parable\Framework\Dispatcher(
            \Parable\DI\Container::get(\Parable\Event\Hook::class),
            $this->mockPath,
            \Parable\DI\Container::get(\Parable\Framework\View::class),
            \Parable\DI\Container::get(\Parable\Http\Response::class)
        );

        // Router is not a Framework class but does depend on other Parable Components
        $this->mockRouter = new \Parable\Routing\Router(
            \Parable\DI\Container::get(\Parable\Http\Request::class),
            \Parable\DI\Container::get(\Parable\Http\Url::class),
            $this->mockPath
        );

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
        $this->mockRouter->addRoute(
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
        $this->mockRouter->addRoute(
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

        $this->mockRouter->addRoute(
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
        $this->mockRouter->addRoute(
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

    public function testAppThrowsExceptionOnInvalidInitLocation()
    {
        $this->expectException(\Parable\Framework\Exception::class);
        $this->expectExceptionMessage("initLocation does not exist:");

        $app = $this->createApp(["initLocations" => ["thisdoesnotexist"]]);
        $app->run();
    }

    public function testAppThrowsExceptionOnInvalidRoute()
    {
        $this->expectException(\Parable\Routing\Exception::class);
        $this->expectExceptionMessage("Either a controller/action combination or callable is required.");

        $routing = $this->createPartialMock(\Routing\App::class, ['get']);
        $routing
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'index' => [
                    'methods' => ['GET'],
                    'url' => '/',
                ],
            ]);

        // Set our fake routing class
        \Parable\DI\Container::store($routing, \Routing\App::class);

        $app = $this->createApp();
        $app->run();

        // And reset
        \Parable\DI\Container::clear(\Routing\App::class);
    }

    /**
     * @return \Parable\Framework\App
     */
    protected function createApp($configValues = null)
    {
        // Config, dispatcher requires Path
        /** @var \Parable\Framework\Config|\PHPUnit_Framework_MockObject_MockObject $config */
        $config = $this->createPartialMock(\Parable\Framework\Config::class, ['getConfig']);
        $config->__construct($this->mockPath);

        if (!is_array($configValues)) {
            $configValues = [
                'app' => [
                    'title'      => 'Parable'
                ],
                'session' => [
                    'autoEnable' => true,
                ],
                'initLocations'  => [
                    'app/Init',
                ],
                'database' => [
                    'type'     => \Parable\ORM\Database::TYPE_MYSQL,
                    'location' => 'localhost',
                    'username' => 'username',
                    'password' => 'password',
                    'database' => 'database',
                ],
                'console' => [
                    'commands' => [
                        \Command\HelloWorld::class,
                    ]
                ]
            ];
        }

        $config
            ->expects($this->any())
            ->method('getConfig')
            ->willReturn($configValues);

        return new \Parable\Framework\App(
            $this->mockPath,
            $config,
            $this->mockDispatcher,
            \Parable\DI\Container::get(\Parable\Event\Hook::class),
            $this->mockRouter,
            \Parable\DI\Container::get(\Parable\Http\Request::class),
            \Parable\DI\Container::get(\Parable\Http\Response::class),
            \Parable\DI\Container::get(\Parable\Http\Url::class),
            \Parable\DI\Container::get(\Parable\GetSet\Session::class),
            \Parable\DI\Container::get(\Parable\ORM\Database::class)
        );
    }
}
