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
        $this->model->username = 'testuser';
        $this->model->password = 'password';

        $modelArray = $this->model->toArray();

        $this->assertSame('testuser', $modelArray['username']);
        $this->assertSame('password', $modelArray['password']);
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

        $this->assertSame('testuser', $modelArray['username']);
        $this->assertArrayNotHasKey('password', $modelArray);
    }
}
