<?php

namespace Parable\Tests\Components\Framework;

class DispatcherTest extends \Parable\Tests\Components\Framework\Base
{
    /** @var \Parable\Framework\Dispatcher */
    protected $dispatcher;

    /** @var \Parable\Event\Hook */
    protected $hook;

    /** @var \Parable\Http\Values\Internal */
    protected $internal;

    protected function setUp()
    {
        parent::setUp();

        $this->dispatcher = \Parable\DI\Container::create(\Parable\Framework\Dispatcher::class);
        $this->hook       = \Parable\DI\Container::get(\Parable\Event\Hook::class);
        $this->internal   = \Parable\DI\Container::get(\Parable\Http\Values\Internal::class);

        $internal = $this->internal;

        $callback = function ($event, $payload) use ($internal) {
            $this->internal->set($event, $payload);
        };

        $this->hook->into('parable_dispatch_before', $callback);
        $this->hook->into('parable_dispatch_after', $callback);
    }

    public function testDispatchRoute()
    {
        $route = new \Parable\Routing\Route(
            new \Parable\Http\Request(),
            [
                'methods' => ['GET'],
                'url' => '/',
                'controller' => \Controller\Home::class,
                'action' => 'index',
            ]
        );

        $this->assertSame($this->dispatcher, $this->dispatcher->dispatch($route));

        $this->assertSame($route, $this->internal->get('parable_dispatch_after'));
    }

}
