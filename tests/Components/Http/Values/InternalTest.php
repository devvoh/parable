<?php

namespace Parable\Tests\Components\Http\Values;

class InternalTest extends \Parable\Tests\Base
{
    /** @var \Parable\Http\Values\Internal */
    protected $getSet;

    protected function setUp()
    {
        parent::setUp();

        $this->getSet = new \Parable\Http\Values\Internal();
    }

    public function testGetResource()
    {
        $this->assertSame("internal", $this->getSet->getResource());
    }

    public function testGetSetOnResource()
    {
        $this->getSet->reset();

        $this->getSet->set("stuff", "here");

        $this->assertSame(
            ["stuff" => "here"],
            $this->getSet->getAll()
        );
    }
}
