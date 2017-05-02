<?php

namespace Parable\Tests\Components\Http\Values;

class PostTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Values\Post */
    protected $getSet;

    protected function setUp()
    {
        parent::setUp();

        $this->getSet = new \Parable\Http\Values\Post();
    }

    public function testGetResource()
    {
        $this->assertSame("_POST", $this->getSet->getResource());
    }

    public function testGetSetOnResource()
    {
        $this->getSet->reset();

        $this->getSet->set("stuff", "here");

        $this->assertSame(
            $this->getSet->getAll(),
            $_POST
        );

        $this->assertSame(
            [
                "stuff" => "here",
            ],
            $_POST
        );
    }
}
