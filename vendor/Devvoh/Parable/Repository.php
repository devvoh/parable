<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

use \Devvoh\Parable\App;

class Repository {

    /** @var null|\Devvoh\Parable\Entity */
    protected $entity       = null;

    /** @var bool */
    protected $onlyCount    = false;

    /** @var array */
    protected $orderBy      = [];

    /** @var array */
    protected $limit        = [];

    /** @var bool */
    protected $returnOne    = false;

    /**
     * Generate a query set to use the current Entity's table name & key
     *
     * @return \Devvoh\Components\Query
     */
    public function createQuery() {
        $query = App::createQuery();
        $query->setTableName($this->getEntity()->getTableName());
        $query->setTableKey($this->getEntity()->getTableKey());
        if ($this->getOnlyCount()) {
            $query->select('count(*)');
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
     * Returns all rows for this entity type
     *
     * @return \Devvoh\Parable\Entity[]|\Devvoh\Parable\Entity
     */
    public function getAll() {
        $query = $this->createQuery();
        $result = App::getDatabase()->query($query);

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
     * Returns a single entity
     *
     * @param int $id
     * @return null|\Devvoh\Parable\Entity
     */
    public function getById($id) {
        $query = $this->createQuery();
        $query->where($this->getEntity()->getTableKey() . ' = ?', $id);
        $result = App::getDatabase()->query($query);

        $entity = null;
        if ($result) {
            $result = $result->fetchAll(\PDO::FETCH_ASSOC);
            $entities = $this->handleResult($result);
            $entity = current($entities);
        }
        return $entity;
    }

    /**
     * Returns all rows matching all conditions passed
     *
     * @param array $conditionsArray
     * @return \Devvoh\Parable\Entity[]|\Devvoh\Parable\Entity
     */
    public function getByConditions(array $conditionsArray) {
        $query = $this->createQuery();
        foreach ($conditionsArray as $condition => $value) {
            $query->where($condition, $value);
        }
        $result = App::getDatabase()->query($query);

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
     * @return $this
     */
    public function orderBy($key, $direction = 'DESC') {
        $this->orderBy = ['key' => $key, 'direction' => $direction];
        return $this;
    }

    /**
     * Sets the limit
     *
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit($limit, $offset = null) {
        $this->limit = ['limit' => $limit, 'offset' => $offset];
        return $this;
    }

    /**
     * Sets the repo to return only one (the first), the same as getById always does
     *
     * @return $this
     */
    public function returnOne() {
        $this->returnOne = true;
        return $this;
    }

    /**
     * Sets the repo to return all values, always in an array (except for getById)
     *
     * @return $this
     */
    public function returnAll() {
        $this->returnOne = false;
        return $this;
    }

    /**
     * Returns all rows matching specific condition given
     *
     * @param string $condition
     * @param string $value
     * @return \Devvoh\Parable\Entity[]|\Devvoh\Parable\Entity
     */
    public function getByCondition($condition, $value) {
        $conditionsArray = [$condition => $value];
        return $this->getByConditions($conditionsArray);
    }

    /**
     * Handle the result of one of the get functions
     *
     * @param array $result
     * @return \Devvoh\Parable\Entity[]|int
     */
    public function handleResult(array $result) {
        /**
         * If we're only counting, return the count result as integer
         */
        if ($this->getOnlyCount()) {
            foreach ($result[0] as $row) {
                return (int)$row;
            }
        }

        /**
         * Not a count, so create entities for every row in the result
         */
        $entities = [];
        foreach ($result as $row) {
            $entity = clone $this->getEntity();
            $entity->populate($row);
            $entities[] = $entity;
        }
        return $entities;
    }

    /**
     * Returns a fresh clone of the stored Entity
     *
     * @return \Devvoh\Parable\Entity|null
     */
    public function createEntity() {
        return clone $this->getEntity();
    }

    /**
     * Set an entity on the repository. Its values don't matter, it'll just be used for configuration purposes.
     *
     * @param \Devvoh\Parable\Entity $entity
     * @return $this
     */
    public function setEntity($entity) {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Return entity
     *
     * @return \Devvoh\Parable\Entity|null
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     * Set onlyCount to true or false
     *
     * @param bool $value
     * @return $this
     */
    public function setOnlyCount($value) {
        $this->onlyCount = (bool)$value;
        return $this;
    }

    /**
     * Return the onlyCount value
     *
     * @return bool
     */
    public function getOnlyCount() {
        return $this->onlyCount;
    }
}