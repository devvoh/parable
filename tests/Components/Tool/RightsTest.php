<?php

namespace Parable\Tests\Components\Tool;

class RightsTest extends \Parable\Tests\Base
{
    /** @var \Parable\Tool\Rights */
    protected $rights;

    protected function setUp()
    {
        parent::setUp();

        $this->rights = $this->diProxy->get(\Parable\Tool\Rights::class);
    }

    public function testDefaultCRUDRightsAreSetProperly()
    {
        $this->assertSame(
            [
                'create' => 1,
                'read'   => 2,
                'update' => 4,
                'delete' => 8,
            ],
            $this->rights->getRights()
        );
    }

    public function testGetRight()
    {
        $this->assertSame(1, $this->rights->getRight('create'));
        $this->assertSame(2, $this->rights->getRight('read'));
        $this->assertSame(4, $this->rights->getRight('update'));
        $this->assertSame(8, $this->rights->getRight('delete'));
    }

    public function testGetNonExistingRightReturnsFalse()
    {
        $this->assertFalse($this->rights->getRight('do_stuff_or_something'));
    }

    public function testAddRight()
    {
        $this->rights->addRight('test');
        $this->assertSame(
            [
                'create' => 1,
                'read'   => 2,
                'update' => 4,
                'delete' => 8,
                'test'   => 16,
            ],
            $this->rights->getRights()
        );
        $this->assertSame(16, $this->rights->getRight('test'));
    }

    public function testCheckRight()
    {
        $this->assertTrue($this->rights->check('10000', 'test'));
        $this->assertTrue($this->rights->check('0000000010000', 'test'));
        $this->assertTrue($this->rights->check(decbin(16), 'test'));

        $this->assertFalse($this->rights->check('01111', 'test'));
    }

    public function testCombine()
    {
        $value = $this->rights->combine(['10000', '00001']);
        $this->assertSame('10001', $value);
    }
}
