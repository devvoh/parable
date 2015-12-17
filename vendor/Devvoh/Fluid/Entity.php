<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  Entity
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid;

use \Devvoh\Fluid\App;

class Entity {
    use \Devvoh\Components\Traits\GetClassName;
    use \Devvoh\Components\Traits\MagicGetSet;

    protected $mapper = null;
    
    /**
     * Generate a query set to use the current Entity's table name & key
     * 
     * @return \Devvoh\Components\Query
     */
    public function createQuery() {
        $query = App::createQuery();
        $query->setTableName($this->getTableName());
        $query->setTableKey($this->getTableKey());
        return $query;
    }
    
    /**
     * Saves the entity, either inserting (no id) or updating (id)
     * 
     * @return mixed
     */
    public function save() {
        $array = $this->toArray();

        $query = $this->createQuery();
    
        if ($this->getId()) {
            $query->setAction('update');
            $query->addValue($this->getTableKey(), $this->id);
    
            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }
        } else {
            $query->setAction('insert');
    
            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }
        }
        return App::getDatabase()->query($query);
    }
    
    /**
     * Generates an array of the current entity, without the protected values
     * 
     * @return array
     */
    public function toArray() {
        $array = (array)$this;
        // Remove protected values & null values, let the database sort those out
        foreach ($array as $key => $value) {
            if (strpos($key, '*') || $value === null) {
                unset($array[$key]);
            }
        }
        // If there's a mapper set, also map the array around
        if ($this->getMapper()) {
            $array = $this->toMappedArray($array);
        }
        return $array;
    }
    
    /**
     * Attempts to use stored mapper array to map fields from the current entity's properties to what is set in the
     * array.
     * 
     * @return array
     */
    public function toMappedArray($array) {
        $mappedArray = [];
        foreach ($this->getMapper() as $from => $to) {
            $mappedArray[$to] = $array[$from];
        }
        return $mappedArray;
    }

    /**
     * Deletes the current entity from the database
     * 
     * @return mixed
     */
    public function delete() {
        $query = $this->createQuery();
        $query->setAction('delete');
        $query->where($this->getTableKey() . ' = ?', $this->getId());
        return App::getDatabase()->query($query);
    }
    
    /**
     * Populates the current entity with the data provided
     * 
     * @param array $data
     *
     * @return \Devvoh\Fluid\Entity
     */
    public function populate($data = []) {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
        return $this;
    }

    public function getMapper() {
        return $this->mapper;
    }
    
}