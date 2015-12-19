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

    protected $tableName    = null;
    protected $tableKey     = null;
    protected $mapper       = null;

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

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        if ($this->id) {
            $query->setAction('update');
            $query->addValue($this->getTableKey(), $this->id);

            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }

            // Since it's an update, add updated_at if the model implements it
            if (property_exists($this, 'updated_at')) {
                $query->addValue('updated_at', $now);
                $this->updated_at = $now;
            }
        } else {
            $query->setAction('insert');

            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }

            // Since it's an insert, add created_at if the model implements it
            if (property_exists($this, 'created_at')) {
                $query->addValue('created_at', $now);
                $this->created_at = $now;
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
        $query->where($this->getTableKey() . ' = ?', $this->id);
        return App::getDatabase()->query($query);
    }

    /**
     * Populates the current entity with the data provided
     *
     * @param array $data
     *
     * @return $this;
     */
    public function populate($data = []) {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
        return $this;
    }

    /**
     * Set the tableName
     *
     * @param $tableName
     * @return $this
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Return the tableName
     *
     * @return string|null
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Set the tableKey
     *
     * @param $tableKey
     * @return $this
     */
    public function setTableKey($tableKey) {
        $this->tableKey = $tableKey;
        return $this;
    }

    /**
     * Return the tableKey
     *
     * @return string|null
     */
    public function getTableKey() {
        return $this->tableKey;
    }

    /**
     * Set the mapper
     *
     * @param $mapper
     * @return $this;
     */
    public function setMapper($mapper) {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Return the mapper
     *
     * @return array|null
     */
    public function getMapper() {
        return $this->mapper;
    }

}