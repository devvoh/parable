<?php

namespace Parable\ORM;

class Repository
{
    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var \Parable\ORM\Model */
    protected $model;

    /** @var bool */
    protected $onlyCount = false;

    /** @var array */
    protected $orderBy = [];

    /** @var array */
    protected $limit = [];

    /** @var bool */
    protected $returnOne = false;

    public function __construct(\Parable\ORM\Database $database)
    {
        $this->database = $database;
    }

    /**
     * Generate a query set to use the current Model's table name & key
     *
     * @return \Parable\ORM\Query
     */
    public function createQuery()
    {
        $query = \Parable\ORM\Query::createInstance();
        $query->setTableName($this->getModel()->getTableName());
        $query->setTableKey($this->getModel()->getTableKey());
        if ($this->getOnlyCount()) {
            $query->select(['count(*)']);
        }
        if (count($this->orderBy)) {
            $query->orderBy($this->orderBy['key'], $this->orderBy['direction']);
        }
        if (count($this->limit)) {
            $query->limit($this->limit['limit'], $this->limit['offset']);
        }
        return $query;
    }

    /**
     * Returns all rows for this model type
     *
     * @return \Parable\ORM\Model[]|\Parable\ORM\Model
     */
    public function getAll()
    {
        $query = $this->createQuery();
        $result = $this->database->query($query);

        $entities = [];
        if ($result) {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            $entities = $this->handleResult($result);
        }
        if ($this->returnOne) {
            return current($entities);
        }
        return $entities;
    }

    /**
     * Returns a single model
     *
     * @param int $id
     *
     * @return null|\Parable\ORM\Model
     */
    public function getById($id)
    {
        $query = $this->createQuery();
        $query->where($this->getModel()->getTableKey(), '=', $id);
        $result = $this->database->query($query);

        $model = null;
        if ($result) {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            $entities = $this->handleResult($result);
            $model = current($entities);
        }
        return $model;
    }

    /**
     * Returns all rows matching all conditions passed
     *
     * @param array $conditionsArray
     *
     * @return \Parable\ORM\Model[]|\Parable\ORM\Model
     */
    public function getByConditions(array $conditionsArray)
    {
        $query = $this->createQuery();
        foreach ($conditionsArray as $conditionArray) {
            $query->where(...$conditionArray);
        }
        $result = $this->database->query($query);

        $entities = [];
        if ($result) {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            $entities = $this->handleResult($result);
        }
        if ($this->returnOne) {
            return current($entities);
        }
        return $entities;
    }

    /**
     * Allow multiple orders by $key in $direction
     *
     * @param string $key
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($key, $direction = 'DESC')
    {
        $this->orderBy = ['key' => $key, 'direction' => $direction];
        return $this;
    }

    /**
     * Sets the limit
     *
     * @param int      $limit
     * @param null|int $offset
     *
     * @return $this
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = ['limit' => $limit, 'offset' => $offset];
        return $this;
    }

    /**
     * Sets the repo to return only one (the first), the same as getById always does
     *
     * @return $this
     */
    public function returnOne()
    {
        $this->returnOne = true;
        return $this;
    }

    /**
     * Sets the repo to return all values, always in an array (except for getById)
     *
     * @return $this
     */
    public function returnAll()
    {
        $this->returnOne = false;
        return $this;
    }

    /**
     * Returns all rows matching specific condition given
     *
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     *
     * @return \Parable\ORM\Model[]|\Parable\ORM\Model
     */
    public function getByCondition($key, $comparator, $value = null)
    {
        return $this->getByConditions([[$key, $comparator, $value]]);
    }

    /**
     * Handle the result of one of the get functions
     *
     * @param array $result
     *
     * @return \Parable\ORM\Model[]|int
     */
    public function handleResult(array $result)
    {
        if ($this->getOnlyCount()) {
            foreach ($result[0] as $row) {
                return (int)$row;
            }
        }

        $entities = [];
        foreach ($result as $row) {
            $model = clone $this->getModel();
            $model->populate($row);
            $entities[] = $model;
        }
        return $entities;
    }

    /**
     * Returns a fresh clone of the stored Model
     *
     * @return null|\Parable\ORM\Model
     */
    public function createModel()
    {
        return clone $this->getModel();
    }

    /**
     * Set an model on the repository. Its values don't matter, it'll just be used for configuration purposes.
     *
     * @param \Parable\ORM\Model $model
     *
     * @return $this
     */
    public function setModel(\Parable\ORM\Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Return model
     *
     * @return null|\Parable\ORM\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set onlyCount to true or false
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setOnlyCount($value)
    {
        $this->onlyCount = (bool)$value;
        return $this;
    }

    /**
     * Return the onlyCount value
     *
     * @return bool
     */
    public function getOnlyCount()
    {
        return $this->onlyCount;
    }
}
