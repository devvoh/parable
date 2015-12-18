<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  Repository
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid;

use \Devvoh\Fluid\App;

class Repository {

    protected $entity = null;

    /**
     * Generate a query set to use the current Entity's table name & key
     *
     * @return \Devvoh\Components\Query
     */
    public function createQuery() {
        $query = App::createQuery();
        $query->setTableName($this->getEntity()->getTableName());
        $query->setTableKey($this->getEntity()->getTableKey());
        return $query;
    }

    /**
     * Returns all rows for this entity type
     * 
     * @return array
     */
    public function getAll() {
        $query = $this->createQuery();
        $result = App::getDatabase()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        
        $entities = [];
        if ($result) {
            foreach ($result as $row) {
                $entity = clone $this->getEntity();
                $entity->populate($row);
                $entities[] = $entity;
            }
        }
        return $entities;
    }

    /**
     * Returns a single entity
     * 
     * @param int $id
     * @return null|\Devvoh\Fluid\Entity
     */
    public function getById($id) {
        $query = $this->createQuery();
        $query->where($this->getEntity()->getTableKey() . ' = ?', $id);
        $result = App::getDatabase()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        
        $entity = null;
        if ($result) {
            $entity = clone $this->getEntity();
            $entity->populate(end($result));
        }
        
        return $entity;
    }

    /**
     * Returns all rows matching all conditions passed
     *
     * @param $conditionsArray
     * @return array
     */
    public function getByConditions($conditionsArray) {
        $query = $this->createQuery();
        foreach ($conditionsArray as $condition => $value) {
            $query->where($condition, $value);
        }
        $result = App::getDatabase()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        $entities = [];
        if ($result) {
            foreach ($result as $row) {
                $entity = clone $this->getEntity();
                $entity->populate($row);
                $entities[] = $entity;
            }
        }
        return $entities;
    }

    /**
     * Returns all rows matching specific condition given
     *
     * @param $condition
     * @param $value
     * @return array
     */
    public function getByCondition($condition, $value) {
        $conditionsArray = [$condition => $value];
        return $this->getByConditions($conditionsArray);
    }
    
    /**
     * Returns a fresh clone of the stored Entity
     * 
     * @return \Devvoh\Fluid\Entity
     */
    public function createEntity() {
        return clone $this->getEntity();
    }

    /**
     * Set an entity on the repository. Its values don't matter, it'll just be used for configuration purposes.
     *
     * @param \Devvoh\Fluid\Entity $entity
     * @return $this
     */
    public function setEntity($entity) {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Return entity
     *
     * @return \Devvoh\Fluid\Entity|null
     */
    public function getEntity() {
        return $this->entity;
    }
}