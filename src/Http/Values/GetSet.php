<?php
/**
 * @package     Parable Routing
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http\Values;

class GetSet
{
    /** @var null|string */
    protected $resource         = null;

    /** @var bool*/
    protected $useLocalResource = false;

    /** @var array */
    protected $localResource    = [];

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
     * @return array|null
     */
    public function getAll()
    {
        if (!$this->getResource()) {
            return null;
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
     * @return mixed|null
     */
    public function get($key)
    {
        if (!$this->getResource()) {
            return null;
        }

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
        return null;
    }

    /**
     * Set specific value by key if resource set
     *
     * @param string $key
     * @param mixed $value
     * @return $this|false
     */
    public function set($key, $value)
    {
        if (!$this->getResource()) {
            return $this;
        }

        // Decide where to store this key/value pair
        if ($this->useLocalResource) {
            $this->localResource[$key] = $value;
        } else {
            $GLOBALS[$this->getResource()][$key] = $value;
        }
        return $this;
    }

    /**
     * Set all key/value pairs in $values if resource is set and $values is an array
     *
     * @param $values
     * @return $this|bool
     */
    public function setMany(array $values)
    {
        if (!$this->getResource()) {
            return $this;
        }

        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    /**
     * Set entire array onto the resource
     *
     * @param array $values
     * @return $this
     */
    public function setAll(array $values)
    {
        if ($this->useLocalResource) {
            $this->localResource[] = $values;
        } else {
            $GLOBALS[$this->getResource()] = $values;
        }
        return $this;
    }

    /**
     * @param $key
     * @return false|GetSet
     */
    public function remove($key)
    {
        return $this->set($key, null);
    }
}
