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

    public function testDatabaseFakesQuotesIfNotConnected()
    {
        /** @var \Parable\ORM\Database $database */
        $database = \Parable\DI\Container::get(\Parable\ORM\Database::class);
        $this->assertSame("'test'", $database->quote('test'));
        $this->assertSame("`test`", $database->quoteIdentifier('test'));
    }

    protected function tearDown()
    {
        parent::tearDown();

        $sql = file_get_contents($this->path->getDir('tests/db/test-teardown.sql'));
        $this->database->getInstance()->exec($sql);
    }
}
