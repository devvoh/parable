<?php

namespace Parable\Tests\Components\ORM;

class ModelTest extends \Parable\Tests\Components\ORM\Base
{
    /** @var \Parable\Tests\TestClasses\Model */
    protected $model;

    protected function setUp()
    {
        parent::setUp();

        $this->model = new \Parable\Tests\TestClasses\Model($this->database);
    }

    public function testModelCreateQuery()
    {
        $query = $this->model->createQuery();

        $this->assertInstanceOf(\Parable\ORM\Query::class, $query);
        $this->assertSame($this->model->getTableName(), $query->getTableName());
    }

    public function testSaveNew()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $this->model->username   = 'new user';
        $this->model->password   = 'password';
        $this->model->email      = 'email@test.dev';
        $this->model->created_at = $now;

        $this->model->save();

        $users = $this->database->query($this->model->createQuery())->fetchAll();
        $this->assertCount(4, $users);

        $this->assertSame('new user', $users[3]['username']);
    }

    public function testPopulateAndSaveExisting()
    {
        $userArray = current($this->database->query($this->model->createQuery())->fetchAll());
        $this->model->populate($userArray);

        $this->assertSame('parable', $this->model->username);

        $this->model->username = 'well this is different';
        $this->assertSame('well this is different', $this->model->username);

        $this->model->save();

        $userArray = current($this->database->query($this->model->createQuery())->fetchAll());
        $this->model->populate($userArray);
        $this->assertSame('well this is different', $this->model->username);
    }

    public function testDelete()
    {
        $result = $this->database->query($this->model->createQuery())->fetchAll();
        $this->assertCount(3, $result);

        foreach ($result as $user) {
            $this->model->id = $user['id'];
            $this->model->delete();
        }

        $result = $this->database->query($this->model->createQuery())->fetchAll();
        $this->assertEmpty($result);
    }

    public function testModelToArray()
    {
        $this->model->username  = 'testuser';
        $this->model->password  = 'password';
        // NULL_VALUE will keep it in the array but set it concretely to null, both on the model's array output
        // and the database queries run with it
        $this->model->email     = \Parable\ORM\Database::NULL_VALUE;
        // Just null keeps it from doing anything, since empty values (other than int 0) are ignored. This prevents
        // queries from attempting to write NULL for every value that's not specifically set. This allows for
        // minimally-populated models to be saved.
        $this->model->create_at = null;

        $this->assertSame(
            [
                'username' => 'testuser',
                'password' => 'password',
                'email'    => null,
            ],
            $this->model->toArray()
        );
    }

    public function testModelToMappedArray()
    {
        $this->model->username = 'testuser';
        $this->model->password = 'password';

        $this->model->setMapper([
            'username' => 'user',
            'password' => 'pass',
        ]);

        $modelArray = $this->model->toArray();

        $this->assertSame('testuser', $modelArray['user']);
        $this->assertSame('password', $modelArray['pass']);
    }

    public function testModelExportToArray()
    {
        $this->model->username = 'testuser';
        $this->model->password = 'password';

        $modelArray = $this->model->exportToArray();

        $this->assertSame(['username', 'email'], $this->model->getExportable());

        $this->assertSame('testuser', $modelArray['username']);
        $this->assertArrayNotHasKey('password', $modelArray);
    }

    public function testSetTableKey()
    {
        $this->model->setTableKey('key');
        $this->assertSame('key', $this->model->getTableKey());
    }

    public function testSetTableName()
    {
        $this->model->setTableName('tableName');
        $this->assertSame('tableName', $this->model->getTableName());
    }

    public function testGuessValueType()
    {
        $this->assertTrue(is_string($this->model->guessValueType("nope")));
        $this->assertFalse(is_float($this->model->guessValueType("nope")));
        $this->assertFalse(is_int($this->model->guessValueType("nope")));

        $this->assertTrue(is_int($this->model->guessValueType("1")));
        $this->assertFalse(is_float($this->model->guessValueType("1")));
        $this->assertFalse(is_string($this->model->guessValueType("1")));

        $this->assertTrue(is_float($this->model->guessValueType("1.23")));
        $this->assertFalse(is_int($this->model->guessValueType("1.23")));
        $this->assertFalse(is_string($this->model->guessValueType("1.23")));
    }
}