<?php

namespace Parable\Tests\Components\Http;

class SessionMessageTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\SessionMessage */
    protected $sessionMessage;

    protected function setUp()
    {
        parent::setUp();

        $session = new \Parable\Http\Values\Session();
        $session->set(
            'messages',
            [
                'notice' => [
                    'This is message 1.',
                    'This is message 2.'
                ],
            ]
        );
        $this->sessionMessage = new \Parable\Http\SessionMessage($session);
    }

    public function testGet()
    {
        $messages = $this->sessionMessage->get();

        $this->assertSame(
            [
                'notice' => ['This is message 1.', 'This is message 2.'],
            ],
            $messages
        );

        $notices = $this->sessionMessage->get('notice');

        $this->assertSame(
            ['This is message 1.', 'This is message 2.'],
            $notices
        );
    }

    public function testAddAndGetClear()
    {
        $this->sessionMessage->add('Test message to be cleared.', 'test');

        $messages = $this->sessionMessage->getClear('test');

        $this->assertSame(
            ['Test message to be cleared.'],
            $messages
        );

        // Should now be empty
        $messages = $this->sessionMessage->get('test');
        $this->assertCount(0, $messages);
    }

    public function testCountAllAndType()
    {
        $this->sessionMessage->add('Test message to be cleared.', 'test');

        $this->assertSame(3, $this->sessionMessage->count());
        $this->assertSame(2, $this->sessionMessage->count('notice'));
        $this->assertSame(1, $this->sessionMessage->count('test'));
    }
}
