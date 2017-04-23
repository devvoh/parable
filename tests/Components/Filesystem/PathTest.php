<?php

namespace Parable\Tests\Components\Filesystem;

class PathTest extends \Parable\Tests\Base
{
    /** @var \Parable\Filesystem\Path */
    protected $path;

    protected function setUp()
    {
        parent::setUp();

        $this->path = $this->diProxy->create(\Parable\Filesystem\Path::class);
    }

    public function testSetAndGetBaseDir()
    {
        $this->path->setBasedir("This is not a valid basedir.");
        $this->assertSame("This is not a valid basedir.", $this->path->getBasedir());
    }

    public function testGetDir()
    {
        $this->path->setBasedir("basedir/");
        $this->assertSame("basedir/stuff", $this->path->getDir("stuff"));
    }
}
