<?php

namespace Parable\GetSet;

abstract class Base
{
    /** @var null|string */
    protected $resource;

    /** @var bool*/
    protected $useLocalResource = false;

    /** @var array */
    protected $localResource = [];

    /**
     * Returns the resource type
     *
     * @return null|string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get all from resource if resource set
     *
     * @return array
     */
    public function getAll()
    {
        if (!$this->getResource()) {
            return [];
        }
        if ($this->useLocalResource) {
            return $this->localResource;
        }
        return $GLOBALS[$this->getResource()];
    }

    /**
     * Get specific value by key if resource set
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if ($this->getResource()) {
            // If local resource, set it as reference
            if ($this->useLocalResource) {
                $reference = $this->localResource;
            } else {
                $reference = $GLOBALS[$this->getResource()];
            }

            // Now check reference and whether the key exists
            if (isset($reference[$key])) {
                return $reference[$key];
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAllAndReset()
    {
        $data = $this->getAll();
        $this->reset();
        return $data;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getAndRemove($key)
    {
        $data = $this->get($key);
        if ($data) {
            $this->remove($key);
        }
        return $data;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->getAll());
    }

    /**
     * Set specific value by key if resource set
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if ($this->getResource()) {
            // Decide where to store this key/value pair
            if ($this->useLocalResource) {
                $this->localResource[$key] = $value;
            } else {
                $GLOBALS[$this->getResource()][$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Set all key/value pairs in $values if resource is set and $values is an array
     *
     * @param array $values
     *
     * @return $this
     */
    public function setMany(array $values)
    {
        if ($this->getResource()) {
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     * Set entire array onto the resource
     *
     * @param array $values
     *
     * @return $this
     */
    public function setAll(array $values)
    {
        if ($this->getResource()) {
            if ($this->useLocalResource) {
                $this->localResource = $values;
            } else {
                $GLOBALS[$this->getResource()] = $values;
            }
        }
        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function remove($key)
    {
        if ($this->getResource()) {
            // Decide where to store this key/value pair
            if ($this->useLocalResource) {
                unset($this->localResource[$key]);
            } else {
                unset($GLOBALS[$this->getResource()][$key]);
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->setAll([]);
        return $this;
    }
}
