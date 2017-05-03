<?php

namespace Parable\Tests\Components\ORM;

class Base extends \Parable\Tests\Base
{
    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    protected function setUp()
    {
        parent::setUp();

        $this->database = new \Parable\ORM\Database();
        $this->path     = \Parable\DI\Container::get(\Parable\Filesystem\Path::class);

        if (!extension_loaded('sqlite3')) {
            $this->markTestSkipped('sqlite3 is not available');
        }

        // Make sure the database file is writable
        if (!is_writable($this->path->getDir('tests/db/test.sqlite3'))) {
            chmod($this->path->getDir('tests/db'), 0777);
        }
        if (!is_writable($this->path->getDir('tests/db/test.sqlite3'))) {
            $this->markTestSkipped('cannot make test database file writable');
        }

        $this->database->setLocation($this->path->getDir('tests/db/test.sqlite3'));
        $this->database->setType(\Parable\ORM\Database::TYPE_SQLITE);

        $sql = file_get_contents($this->path->getDir('tests/db/test-setup.sql'));
        $this->database->getInstance()->exec($sql);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $sql = file_get_contents($this->path->getDir('tests/db/test-teardown.sql'));
        $this->database->getInstance()->exec($sql);
    }
}
