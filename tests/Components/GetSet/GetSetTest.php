<?php

namespace Parable\Tests\Components\GetSet;

class GetSetTest extends \Parable\Tests\Base
{
    /** @var \Parable\Tests\TestClasses\TestGetSet */
    protected $getSet;

    protected function setUp()
    {
        parent::setUp();

        $this->getSet = new \Parable\Tests\TestClasses\TestGetSet();
    }

    public function testGetResource()
    {
        $this->assertSame('test', $this->getSet->getResource());
    }

    public function testSetAllGetAll()
    {
        $this->getSet->reset();

        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        $this->assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
            ],
            $this->getSet->getAll()
        );
    }

    public function testGetAllAndReset()
    {
        $this->getSet->reset();

        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        $this->assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
            ],
            $this->getSet->getAllAndReset()
        );

        $this->assertSame([], $this->getSet->getAll());
    }

    public function testGetAndRemove()
    {
        $this->getSet->reset();

        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        $this->assertSame(
            'yo1',
            $this->getSet->getAndRemove('key1')
        );

        $this->assertSame(
            [
                'key2' => 'yo2',
            ],
            $this->getSet->getAll()
        );
        $this->assertSame(1, $this->getSet->count());
    }

    public function testSetSpecificGetSpecificAndGetAll()
    {
        $this->getSet->reset();

        $this->getSet->set('key1', 'yo1');
        $this->getSet->set('key2', 'yo2');
        $this->getSet->set('key3', 'yo3');

        $this->assertSame('yo3', $this->getSet->get('key3'));

        $this->assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
                'key3' => 'yo3',
            ],
            $this->getSet->getAll()
        );
    }

    public function testSetAllVersusSetMany()
    {
        $this->getSet->reset();

        $this->assertCount(0, $this->getSet->getAll());

        $this->getSet->setAll([
            'temp1' => 'yo1',
            'temp2' => 'yo2',
            'temp3' => 'yo3',
        ]);

        $this->assertCount(3, $this->getSet->getAll());

        $this->assertSame(
            [
                'temp1' => 'yo1',
                'temp2' => 'yo2',
                'temp3' => 'yo3',
            ],
            $this->getSet->getAll()
        );

        // setAll() overwrites all values, discarding the pre-existing ones
        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        $this->assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
            ],
            $this->getSet->getAll()
        );

        // setMany() overwrites all values passed if they exist, but leaves pre-existing ones intact
        $this->getSet->setMany([
            'key1' => 'this is new',
            'key3' => 'this is new as well',
        ]);

        $this->assertSame(
            [
                'key1' => 'this is new',
                'key2' => 'yo2',
                'key3' => 'this is new as well',
            ],
            $this->getSet->getAll()
        );
    }

    public function testGetAllReturnsEmptyArrayIfNoResourceSet()
    {
        $getset = new \Parable\Tests\TestClasses\TestGetSetNoResource();
        $this->assertSame([], $getset->getAll());
    }
}
