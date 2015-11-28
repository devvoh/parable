<?php
/**
 * @package     Fluid
 * @subpackage  App
 * @subpackage  Database
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Components;

class Database {
    use \Devvoh\Components\Traits\GetClassName;

    protected $type     = null;
    protected $location = null;
    protected $username = null;
    protected $password = null;
    protected $database = null;
    protected $instance = null;

    /**
     * Returns the type, if any
     *
     * @return string|null
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set the type
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the location, if any
     *
     * @return string|null
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set the location
     *
     * @param $location
     *
     * @return $this
     */
    public function setLocation($location) {
        $this->location = $location;
        return $this;
    }

    /**
     * Return the username
     *
     * @return null
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set the username
     *
     * @param $username
     *
     * @return $this
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * Return the password
     *
     * @return null
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set the password
     *
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * Return the database, if any
     *
     * @return string|null
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * Set the database
     *
     * @param $database
     *
     * @return $this
     */
    public function setDatabase($database) {
        $this->database = $database;
        return $this;
    }

    /**
     * Return instance, if any
     *
     * @return PDO|null
     */
    public function getInstance() {
        if (!$this->getInstance() && $this->getType() && $this->getLocation()) {
            switch ($this->getType()) {
                case 'sqlite3':
                    $instance = new PDO('sqlite:' . $this->getLocation());
                    $this->setInstance($instance);
                    break;
                case 'mysql':
                    if (!$this->getUsername() || !$this->getPassword() || !$this->getDatabase()) {
                        return false;
                    }
                    $instance = new PDO('mysql:host=' . $this->getLocation() . ';dbname=' . $this->getDatabase(), $this->getUsername(), $this->getPassword());
                    $this->setInstance($instance);
            }
        }
        return $this->instance;
    }

    /**
     * Sets the instance
     *
     * @param $instance
     *
     * @return $this
     */
    public function setInstance($instance) {
        $this->instance = $instance;
        return $this;
    }

    /**
     * If an instance is available, quote/escape the message through PDO's quote function
     *
     * @param $string
     *
     * @return bool|string
     */
    public function quote($string) {
        if (!$this->getInstance()) {
            return false;
        }
        return $this->getInstance()->quote($string);
    }

}