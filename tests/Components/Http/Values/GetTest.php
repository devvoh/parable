<?php

namespace Parable\Tests\Components\Http\Values;

class GetTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Values\Get */
    protected $getSet;

    protected function setUp()
    {
        parent::setUp();

        $this->getSet = new \Parable\Http\Values\Get();
    }

    public function testGetResource()
    {
        $this->assertSame("_GET", $this->getSet->getResource());
    }

    public function testGetSetOnResource()
    {
        $this->getSet->reset();

        $this->getSet->set("stuff", "here");

        $this->assertSame(
            $this->getSet->getAll(),
            $_GET
        );

        $this->assertSame(
            [
                "stuff" => "here",
            ],
            $_GET
        );
    }

    public function testSetAndRemove()
    {
        $this->getSet->reset();

        $this->getSet->set('test', 'value');

        $this->assertSame('value', $this->getSet->get('test'));

        $this->getSet->remove('test');

        $this->assertNull($this->getSet->get('test'));
    }
}
