<?php

namespace Parable\Tests\Components\ORM;

class DatabaseTest extends \Parable\Tests\Components\ORM\Base
{
    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var \Parable\Filesystem\Path */
    protected $path;

    public function testDatabaseDirectQuery()
    {
        $user = current($this->database->query('select * from user')->fetchAll());
        $this->assertSame(
            [
                "id"         => '1',
                "username"   => "parable",
                "password"   => "plaintextpasswordsarebad",
                "email"      => "parable@test.dev",
                "created_at" => "2016-01-01 10:00:00",
                "updated_at" => null,
            ],
            $user
        );
    }

    public function testGetType()
    {
        $this->assertSame(\Parable\ORM\Database::TYPE_SQLITE, $this->database->getType());
    }

    public function testGetLocation()
    {
        $this->assertSame(\Parable\ORM\Database::LOCATION_SQLITE_MEMORY, $this->database->getLocation());
    }

    public function testSetAndGetUsername()
    {
        $this->database->setUsername('username');
        $this->assertSame('username', $this->database->getUsername());
    }

    public function testSetAndGetPassword()
    {
        $this->database->setPassword('password');
        $this->assertSame('password', $this->database->getPassword());
    }

    public function testSetAndGetDatabase()
    {
        $this->database->setDatabase('db');
        $this->assertSame('db', $this->database->getDatabase());
    }

    public function testGetInstanceReturnsWorkingPDOInstance()
    {
        $this->assertInstanceOf(\PDO::class, $this->database->getInstance());
    }

    public function testQuoteIdentifierUsesBackwardTicksRegardlessOfInstance()
    {
        $this->assertSame("`test`", $this->database->quoteIdentifier('test'));

        $database = \Parable\DI\Container::create(\Parable\ORM\Database::class);
        $this->assertSame("`test`", $database->quoteIdentifier('test'));
    }

    public function testDatabaseQuotesWithInstance()
    {
        $this->assertSame("'test'", $this->database->quote('test'));
    }

    public function testThrowsExceptionWhenQuotingValueWithoutInstance()
    {
        /** @var \Parable\ORM\Database $database */
        $database = \Parable\DI\Container::create(\Parable\ORM\Database::class);
        $this->assertNull($database->getInstance());

        $this->expectException(\Parable\ORM\Exception::class);
        $this->expectExceptionMessage("Can't quote value without a database instance.");
        $database->quote("test");
    }

    public function testThrowsExceptionWhenRunningQueryWithoutInstance()
    {
        /** @var \Parable\ORM\Database $database */
        $database = \Parable\DI\Container::create(\Parable\ORM\Database::class);
        $this->assertNull($database->getInstance());

        $this->expectException(\Parable\ORM\Exception::class);
        $this->expectExceptionMessage("Can't run query without a database instance.");
        $database->query("select * from `user`");
    }

    protected function tearDown()
    {
        parent::tearDown();

        $sql = file_get_contents($this->path->getDir('tests/db/test-teardown.sql'));
        $this->database->getInstance()->exec($sql);
    }
}
