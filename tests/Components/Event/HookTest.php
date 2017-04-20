<?php

namespace Parable\Tests\Components\Event;

class HookTest extends \Parable\Tests\Base
{
    /** @var \Parable\Event\Hook */
    protected $dock;

    protected function setUp()
    {
        parent::setUp();

        $this->hook = $this->di->create(\Parable\Event\Hook::class);
    }
    public function testIntoAndTriggerBasic()
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
}
