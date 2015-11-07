<?php
/**
 * @package     Fluid
 * @subpackage  App/Database
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

use Devvoh\Fluid\App as App;

class Database {

    protected $type     = null;
    protected $location = null;
    protected $username = null;
    protected $password = null;
    protected $database = null;
    protected $instance = null;

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

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($type) {
        $this->type = $type;
        return $this;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function setDatabase($type) {
        $this->type = $type;
        return $this;
    }

    public function getInstance() {
        return $this->instance;
    }

    public function setInstance($type) {
        $this->type = $type;
        return $this;
    }

    public function quote($string) {
        if (!$this->getInstance()) {
            return false;
        }
        return $this->getInstance()->quote($string);
    }

}