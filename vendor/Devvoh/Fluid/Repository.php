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
    use \Devvoh\Components\Traits\GetClassName;
    use \Devvoh\Components\Traits\MagicGetSet;

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
     * @return []
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
     * Returns a fresh clone of the stored Entity
     * 
     * @return \Devvoh\Fluid\Entity
     */
    public function createEntity() {
        return clone $this->getEntity();
    }
}