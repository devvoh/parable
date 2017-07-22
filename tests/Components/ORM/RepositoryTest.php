<?php

namespace Parable\Tests\Components\ORM;

class RepositoryTest extends \Parable\Tests\Components\ORM\Base
{
    /** @var \Parable\ORM\Model */
    protected $model;

    /** @var \Parable\ORM\Repository */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Parable\ORM\Model $model */
        $this->model = \Parable\DI\Container::create(\Parable\Tests\TestClasses\Model::class);

        /** @var \Parable\ORM\Repository $repository */
        $this->repository = \Parable\DI\Container::create(\Parable\ORM\Repository::class);

        $this->repository->setModel($this->model);
    }

    public function testCreateQuery()
    {
        $query = $this->repository->createQuery();

        $this->assertInstanceOf(\Parable\ORM\Query::class, $query);
        $this->assertSame($this->model->getTableName(), $query->getTableName());
    }

    public function testGetModelCreateModelNotTheSame()
    {
        $this->model->username = 'original';

        // Normally there's no values set on a Repository, since it's a 'reference' model.
        $model_original = $this->repository->getModel();
        $this->assertSame('original', $model_original->username);

        // createModel clones the model currently stored and resets all public values
        $model_clone = $this->repository->createModel();
        $this->assertEmpty($model_clone->username);
    }

    public function setModelResetsModel()
    {
        $this->model->username = 'original';
        $this->repository->setModel($this->model);

        $this->assertEmpty($this->repository->getModel());
    }

    public function testGetAll()
    {
        $users = $this->repository->getAll();
        $this->assertCount(3, $users);

        $this->assertSame('parable', $users[0]->username);
        $this->assertSame('test', $users[1]->username);
        $this->assertSame('user', $users[2]->username);
    }

    /**
     * @dataProvider dpUserIdsAndUsernames
     *
     * @param int    $id
     * @param string $username
     */
    public function testGetById($id, $username)
    {
        $user = $this->repository->getById($id);
        $this->assertSame($username, $user->username);
    }

    /**
     * @dataProvider dpUserIdsAndUsernames
     *
     * @param int    $id
     * @param string $username
     */
    public function testGetByConditionDefaultAnd($id, $username)
    {
        $userResult = $this->repository->getByCondition('id', '=', $id);
        $this->assertSame($username, $userResult[0]->username);
    }

    /**
     * @dataProvider dpUserIdsAndUsernames
     *
     * @param int    $id
     * @param string $username
     */
    public function testGetByConditionOr($id, $username)
    {
        $userResult = $this->repository->getByCondition('id', '=', $id, \Parable\ORM\Query\ConditionSet::SET_OR);
        $this->assertSame($username, $userResult[0]->username);
    }

    public function testGetByConditionThrowsExceptionOnInvalidAndOrType()
    {
        $this->expectExceptionMessage("Invalid andOr type given.");
        $this->expectException(\Parable\ORM\Exception::class);
        $userResult = $this->repository->getByCondition('id', '=', 1, 'maybe');
    }

    /**
     * @dataProvider dpUserIdsAndUsernames
     *
     * @param int    $id
     * @param string $username
     */
    public function testGetByConditionSet($id, $username)
    {
        $query = $this->repository->createQuery();

        $userResult = $this->repository->getByConditionSet($query->buildAndSet(['id', '=', $id]));

        $this->assertSame($username, $userResult[0]->username);
    }

    /**
     * @dataProvider dpUserIdsAndUsernames
     *
     * @param int    $id
     * @param string $username
     */
    public function testGetByConditionSets($id, $username)
    {
        $query = $this->repository->createQuery();

        $userResult = $this->repository->getByConditionSets(
            [
                $query->buildAndSet([
                    ['id', '=', $id],
                    ['username', '=', $username],
                ])
            ]
        );

        $this->assertSame($username, $userResult[0]->username);
    }

    public function testGetAnyTypeReturnsEmptyAndFalseEquivalentOnNothingFound()
    {
        // getById is always singular
        $result = $this->repository->getById(8);
        $this->assertEmpty($result);
        $this->assertTrue(!$result);

        // getByCondition is plural by default
        $result = $this->repository->getByCondition('id', '=', 8);
        $this->assertEmpty($result);
        $this->assertTrue(!$result);

        // the repository can be set to only return 1 result
        $result = $this->repository->returnOne()->getByCondition('id', '=', 8);
        $this->assertEmpty($result);
        $this->assertTrue(!$result);

        // the repository can be set to only count
        $result = $this->repository->onlyCount(true)->getByCondition('id', '=', 8);
        $this->assertEmpty($result);
        $this->assertTrue(!$result);
    }

    public function testOrderBy()
    {
        // ASC is the default
        $result = $this->repository->getAll();

        $this->assertSame($result[0]->id, 1);
        $this->assertSame($result[1]->id, 2);
        $this->assertSame($result[2]->id, 3);

        // Go to DESC
        $result = $this->repository->orderBy('id', \Parable\ORM\Query::ORDER_DESC)->getAll();

        $this->assertSame($result[0]->id, 3);
        $this->assertSame($result[1]->id, 2);
        $this->assertSame($result[2]->id, 1);

        // And back to ASC
        $result = $this->repository->orderBy('id', \Parable\ORM\Query::ORDER_ASC)->getAll();

        $this->assertSame($result[0]->id, 1);
        $this->assertSame($result[1]->id, 2);
        $this->assertSame($result[2]->id, 3);
    }

    public function testLimitOffset()
    {
        $result = $this->repository->limitOffset(1)->getAll();

        $this->assertSame($result[0]->id, 1);

        $result = $this->repository->limitOffset(1, 1)->getAll();

        $this->assertSame($result[0]->id, 2);

        $result = $this->repository->limitOffset(1, 2)->getAll();

        $this->assertSame($result[0]->id, 3);
    }

    public function testReturnOneAndReturnAll()
    {
        $result = $this->repository->getAll();
        $this->assertCount(3, $result);

        $result = $this->repository->returnOne()->getAll();
        $this->assertInstanceOf(\Parable\ORM\Model::class, $result);

        $result = $this->repository->returnAll()->getAll();
        $this->assertCount(3, $result);
    }

    public function testOnlyCount()
    {
        $this->repository->onlyCount(true);
        $this->assertSame(3, $this->repository->returnOne()->getAll());
    }

    public function dpUserIdsAndUsernames()
    {
        return [
            [1, 'parable'],
            [2, 'test'],
            [3, 'user'],
        ];
    }

    public function testBuildAndOrSets()
    {
        $set = $this->repository->buildAndSet([
            ['id', '=', 1],
        ]);

        $this->assertInstanceOf(\Parable\ORM\Query\Condition\AndSet::class, $set);
        $this->assertSame(\Parable\ORM\Query\ConditionSet::SET_AND, $set::TYPE);

        $set = $this->repository->buildOrSet([
            ['id', '=', 1],
        ]);

        $this->assertInstanceOf(\Parable\ORM\Query\Condition\OrSet::class, $set);
        $this->assertSame(\Parable\ORM\Query\ConditionSet::SET_OR, $set::TYPE);
    }
}
