<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class GetSet {

    /**
     * @var null|string
     */
    protected $resource         = null;

    /**
     * @var bool
     */
    protected $useLocalResource = false;

    /**
     * @var array
     */
    protected $localResource    = [];

    /**
     * @var array
     */
    protected $globals          = ['get', 'post', 'session', 'cookie'];

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
     * @param $type
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
     * @return array|null
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
     * @param $key
     *
     * @return mixed|null
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
     * @param $key
     * @param $value
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
     * @param $values
     * @return $this|bool
     */
    public function setMany($values) {
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
     * @param $values
     *
     * @return $this
     */
    public function setAll($values) {
        if ($this->useLocalResource) {
            $this->localResource[$this->getResource()] = $values;
        } else {
            $GLOBALS['_' . $this->getResource()] = $values;
        }
        return $this;
    }

    /**
     * Start the session
     *
     * @return $this
     */
    public function startSession() {
        session_start();
        return $this;
    }

    /**
     * Destroy the session
     *
     * @return $this
     */
    public function destroySession() {
        session_destroy();
        return $this;
    }

}