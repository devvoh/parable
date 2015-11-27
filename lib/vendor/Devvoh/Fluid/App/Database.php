<?php
/**
 * @package     Fluid
 * @subpackage  App
 * @subpackage  Database
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

use \Devvoh\Fluid\App as App;

class Database {
    use \Devvoh\Components\Traits\GetClassName;

    protected $type     = null;
    protected $location = null;
    protected $username = null;
    protected $password = null;
    protected $database = null;
    protected $instance = null;

    /**
     * If relevant data is set, create the DB instance and save it as $instance
     *
     * @return mixed
     */
    public function __construct() {
        if (!$this->type || !$this->location) {
            return false;
        }

        switch ($this->type) {
            case 'sqlite3':
                $this->instance = new PDO('sqlite:' . App::getBaseDir() . $this->location);
                break;
            case 'mysql':
                if (!$this->username || !$this->password || !$this->database) {
                    return false;
                }
                $this->instance = new PDO('mysql:host=' . $this->location . ';dbname=' . $this->database, $this->username, $this->password);
        }
        return $this;
    }

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