<?php

namespace Parable\Tests\Components\Http\Values;

class CookieTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Values\Cookie */
    protected $getSet;

    protected function setUp()
    {
        parent::setUp();

        $this->getSet = new \Parable\Http\Values\Cookie();
    }

    public function testGetResource()
    {
        $this->assertSame("_COOKIE", $this->getSet->getResource());
    }

    public function testGetSetOnResource()
    {
        $this->getSet->reset();

        $this->getSet->set("stuff", "here");

        $this->assertSame(
            $this->getSet->getAll(),
            $_COOKIE
        );

        $this->assertSame(
            [
                "stuff" => "here",
            ],
            $_COOKIE
        );
    }
}
