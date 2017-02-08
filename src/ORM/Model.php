<?php

namespace Parable\ORM;

class Model
{
    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var array */
    protected $mapper = [];

    /** @var array */
    protected $exportable = [];

    /** @var null|int */
    public $id;

    /** @var null|string */
    protected $tableName;

    /** @var null|string */
    protected $tableKey;

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
        $query->setTableName($this->getTableName());
        $query->setTableKey($this->getTableKey());
        return $query;
    }

    /**
     * Saves the model, either inserting (no id) or updating (id)
     *
     * @return mixed
     */
    public function save()
    {
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
                if ($key !== $this->tableKey) {
                    $query->addValue($key, $value);
                }
            }

            // Since it's an insert, add created_at if the model implements it
            if (property_exists($this, 'created_at')) {
                $query->addValue('created_at', $now);
                $this->created_at = $now;
            }
        }
        $result = $this->database->query($query);
        if ($result && $query->getAction() === 'insert') {
            $this->id = $this->database->getInstance()->lastInsertId();
        }
        return $result;
    }

    /**
     * Generates an array of the current model, without the protected values
     *
     * @return array
     */
    public function toArray()
    {
        $array = (array)$this;
        // Remove protected values & null values, let the database sort those out
        foreach ($array as $key => &$value) {
            if (strpos($key, '*') !== false) {
                unset($array[$key]);
                continue;
            }
            if ($value === 'null') {
                $value = null;
                continue;
            }
            if ($value !== 0 && empty($value)) {
                unset($array[$key]);
                continue;
            }
        }
        // If there's a mapper set, also map the array around
        if ($this->getMapper()) {
            $array = $this->toMappedArray($array);
        }
        return $array;
    }

    /**
     * Attempts to use stored mapper array to map fields from the current model's properties to what is set in the
     * array.
     *
     * @param array $array
     *
     * @return array
     */
    public function toMappedArray(array $array)
    {
        $mappedArray = [];
        foreach ($this->getMapper() as $from => $to) {
            $mappedArray[$to] = $array[$from];
        }
        return $mappedArray;
    }

    /**
     * Deletes the current model from the database
     *
     * @return mixed
     */
    public function delete()
    {
        $query = $this->createQuery();
        $query->setAction('delete');
        $query->where($this->getTableKey(), '=', $this->id);
        return $this->database->query($query);
    }

    /**
     * Populates the current model with the data provided
     *
     * @param array $data
     *
     * @return $this;
     */
    public function populate(array $data)
    {
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
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Return the tableName
     *
     * @return null|string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set the tableKey
     *
     * @param string $tableKey
     *
     * @return $this
     */
    public function setTableKey($tableKey)
    {
        $this->tableKey = $tableKey;
        return $this;
    }

    /**
     * Return the tableKey
     *
     * @return null|string
     */
    public function getTableKey()
    {
        return $this->tableKey;
    }

    /**
     * Set the mapper
     *
     * @param array $mapper
     *
     * @return $this;
     */
    public function setMapper(array $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Return the mapper
     *
     * @return array
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Returns the exportable array
     *
     * @return array
     */
    public function getExportable()
    {
        return $this->exportable;
    }

    /**
     * Export to array, which will exclude unexportable keys
     *
     * @return array
     */
    public function exportToArray()
    {
        $exportable = $this->getExportable();
        $data = $this->toArray();
        $exportData = [];
        foreach ($exportable as $key) {
            $exportData[$key] = $data[$key];
        }
        return $exportData;
    }
}
