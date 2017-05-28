<?php

namespace Parable\Tests\Components\Event;

class HookTest extends \Parable\Tests\Base
{
    /** @var \Parable\Event\Hook */
    protected $hook;

    protected function setUp()
    {
        parent::setUp();

        $this->hook = \Parable\DI\Container::create(\Parable\Event\Hook::class);
    }

    public function testTriggerWithoutAnythingReturnsHook()
    {
        $this->assertSame($this->hook, $this->hook->trigger('event_that_does_not_exist'));
    }

    public function testTriggerGlobalReturnsHook()
    {
        $this->assertSame($this->hook, $this->hook->trigger('*'));
    }

    public function testIntoAddsCallable()
    {
        $this->hook->into('test_hook_into', function ($event, &$payload) {
        });

        $hooks = $this->liberateProperty($this->hook, 'hooks');
        $this->assertCount(1, $hooks);
        $this->assertArrayHasKey('test_hook_into', $hooks);

        $callable = $hooks['test_hook_into'][0];

        $this->assertNotEmpty($callable);

        $this->assertTrue(is_callable($callable));
    }

    public function testIntoAndTriggerBasicWithStringPayload()
    {
        $this->hook->into('test_hook_into', function ($event, &$string) {
            $this->assertSame('test_hook_into', $event);
            $string .= ", world!";
        });

        $payload = "Hello";
        $this->hook->trigger('test_hook_into', $payload);

        $this->assertSame("Hello, world!", $payload);
    }

    public function testWithObjectPayload()
    {
        $object = new \stdClass();
        $object->firstName = "Hello";
        $object->lastName  = null;

        $this->hook->into('test_hook_into', function ($event, &$payload) {
            $payload->lastName = "World";
        });

        $this->hook->trigger('test_hook_into', $object);

        $this->assertSame("Hello", $object->firstName);
        $this->assertSame("World", $object->lastName);
    }

    public function testWithArrayPayload()
    {
        $array = [
            'firstName' => "Hello",
            'lastName'  => null,
        ];

        $this->hook->into('test_hook_into', function ($event, &$payload) {
            $payload['lastName'] = "World";
        });

        $this->hook->trigger('test_hook_into', $array);

        $this->assertSame("Hello", $array['firstName']);
        $this->assertSame("World", $array['lastName']);
    }

    public function testTriggerCanTriggerMultipleHooks()
    {
        $this->hook->into('test_dock_into', function ($event, &$payload) {
            $payload .= '1';
        });
        $this->hook->into('test_dock_into', function ($event, &$payload) {
            $payload .= '2';
        });
        $this->hook->into('test_dock_into', function ($event, &$payload) {
            $payload .= '3!';
        });

        $payload = 'Testing... ';
        $this->hook->trigger('test_dock_into', $payload);

        $this->assertSame('Testing... 123!', $payload);
    }

    public function testGlobalIntoRespondsToAllTriggers()
    {
        $this->hook->into('*', function ($event, &$payload) {
            $payload .= ".{$event}";
        });

        $payload = "Triggering";
        $this->hook->trigger('random_1', $payload);
        $this->hook->trigger('random_2', $payload);
        $this->hook->trigger('random_3', $payload);

        $this->assertSame('Triggering.random_1.random_2.random_3', $payload);
    }
}
