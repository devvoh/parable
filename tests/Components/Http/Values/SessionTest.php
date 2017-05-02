<?php

namespace Parable\Tests\Components\Http\Values;

class SessionTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Values\Session */
    protected $getSet;

    protected function setUp()
    {
        parent::setUp();

        $this->getSet = new \Parable\Http\Values\Session();
    }

    public function testGetResource()
    {
        $this->assertSame("_SESSION", $this->getSet->getResource());
    }

    public function testGetSetOnResource()
    {
        $this->getSet->reset();

        $this->getSet->set("stuff", "here");

        $this->assertSame(
            $this->getSet->getAll(),
            $_SESSION
        );

        $this->assertSame(
            [
                "stuff" => "here",
            ],
            $_SESSION
        );
    }
}
