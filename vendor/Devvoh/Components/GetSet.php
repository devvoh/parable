<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class GetSet {

    /** @var bool*/
    protected $useLocalResource = false;

    /** @var array */
    protected $localResource    = [];

    /** @var array */
    protected $globals          = ['get', 'post', 'session', 'cookie'];

    /** @var null|string */
    protected $resource;

    /**
     * Return globals
     *
     * @return array
     */
    public function getGlobals() {
        return $this->globals;
    }

    /**
     * Returns the resource type
     *
     * @return null|string
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * Sets the resource type, which can be either a PHP superglobal (GET/POST/SESSION, etc) or a custom one.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setResource($type) {
        $this->resource = $type;

        if (in_array(strtolower($type), $this->getGlobals())) {
            $this->useLocalResource = false;
            $this->resource = strtoupper($type);
        } else {
            $this->useLocalResource = true;
            if (!isset($this->localResource[$this->getResource()])) {
                $this->localResource[$this->getResource()] = [];
            }
        }
        return $this;
    }

    /**
     * Get all from resource if resource set
     *
     * @return null|array
     */
    public function getAll() {
        if (!$this->getResource()) {
            return null;
        }
        if ($this->useLocalResource) {
            return $this->localResource[$this->getResource()];
        }
        return $GLOBALS['_' . $this->getResource()];
    }

    /**
     * Get specific value by key if resource set
     *
     * @param string $key
     *
     * @return null|mixed
     */
    public function get($key) {
        if (!$this->getResource()) {
            return null;
        }

        // If local resource, set it as reference
        if ($this->useLocalResource) {
            $reference = $this->localResource[$this->getResource()];
        } else {
            $reference = $GLOBALS['_' . $this->getResource()];
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
     * @param mixed  $value
     *
     * @return $this|false
     */
    public function set($key, $value) {
        if (!$this->getResource()) {
            return false;
        }

        // Decide where to store this key/value pair
        if ($this->useLocalResource) {
            $this->localResource[$this->getResource()][$key] = $value;
        } else {
            $GLOBALS['_' . $this->getResource()][$key] = $value;
        }
        return $this;
    }

    /**
     * Set all key/value pairs in $values if resource is set and $values is an array
     *
     * @param array $values
     *
     * @return $this|false
     */
    public function setMany(array $values) {
        if (!$this->getResource() || !is_array($values)) {
            return false;
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
     *
     * @return $this
     */
    public function setAll(array $values) {
        if ($this->useLocalResource) {
            $this->localResource[$this->getResource()] = $values;
        } else {
            $GLOBALS['_' . $this->getResource()] = $values;
        }
        return $this;
    }

    /**
     * Remove a key from the resource
     *
     * @param string $key
     *
     * @return $this|false
     */
    public function remove($key) {
        return $this->set($key, null);
    }

}
