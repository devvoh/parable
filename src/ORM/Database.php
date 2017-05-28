<?php

namespace Parable\ORM;

class Database
{
    /** Use this to set a value to SQL NULL */
    const NULL_VALUE = '__parable_null_value__';

    /** Types supported */
    const TYPE_SQLITE = 'sqlite';
    const TYPE_MYSQL  = 'mysql';

    /** SQLite-specific location */
    const LOCATION_SQLITE_MEMORY = ':memory:';

    /** @var null|string */
    protected $type;

    /** @var null|string */
    protected $location;

    /** @var null|string */
    protected $username;

    /** @var null|string */
    protected $password;

    /** @var null|string */
    protected $database;

    /** @var null|\PDO */
    protected $instance;

    /**
     * Returns the type, if any
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the location, if any
     *
     * @return null|string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the location
     *
     * @param string $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Return the username
     *
     * @return null|string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the username
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Return the password
     *
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the password
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Return the database, if any
     *
     * @return null|string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set the database
     *
     * @param string $database
     *
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Return instance, if any
     *
     * @return null|\PDO
     */
    public function getInstance()
    {
        if (!$this->instance && $this->getType() && $this->getLocation()) {
            switch ($this->getType()) {
                case static::TYPE_SQLITE:
                    $instance = $this->createPDOSQLite('sqlite:' . $this->getLocation());
                    $this->setInstance($instance);
                    break;
                case static::TYPE_MYSQL:
                    if (!$this->getUsername() || !$this->getPassword() || !$this->getDatabase()) {
                        return null;
                    }
                    $instance = $this->createPDOMySQL(
                        'mysql:host=' . $this->getLocation() . ';dbname=' . $this->getDatabase(),
                        $this->getUsername(),
                        $this->getPassword()
                    );
                    $this->setInstance($instance);
            }
        }
        return $this->instance;
    }

    /**
     * @param string $dsn
     *
     * @return \Parable\ORM\Database\PDOSQLite
     */
    protected function createPDOSQLite($dsn)
    {
        return new \Parable\ORM\Database\PDOSQLite($dsn);
    }

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     *
     * @return \Parable\ORM\Database\PDOMySQL
     *
     * @codeCoverageIgnore
     */
    protected function createPDOMySQL($dsn, $username, $password)
    {
        return new \Parable\ORM\Database\PDOMySQL($dsn, $username, $password);
    }

    /**
     * Sets the instance
     *
     * @param \PDO $instance
     *
     * @return $this
     */
    public function setInstance(\PDO $instance)
    {
        $this->instance = $instance;
        return $this;
    }

    /**
     * If an instance is available, quote/escape the message through PDO's quote function
     *
     * @param string $string
     *
     * @return null|string
     * @throws \Parable\ORM\Exception
     */
    public function quote($string)
    {
        if (!$this->getInstance()) {
            throw new \Parable\ORM\Exception("Can't quote value without a database instance.");
        }
        return $this->getInstance()->quote($string);
    }

    /**
     * Identifiers need to be escaped differently than values, using ` characters. PDO by default does not offer this.
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteIdentifier($string)
    {
        return '`' . (string)$string . '`';
    }

    /**
     * Passes $query on to the PDO instance if it's successfully initialized. If not, returns false. If so, returns
     * PDO result object.
     *
     * @param string $query
     *
     * @return \PDOStatement
     * @throws \Parable\ORM\Exception
     */
    public function query($query)
    {
        if (!$this->getInstance()) {
            throw new \Parable\ORM\Exception("Can't run query without a database instance.");
        }
        return $this->getInstance()->query($query, \PDO::FETCH_ASSOC);
    }

    /**
     * Use an array to pass multiple config values at the same time
     *
     * @param array $config
     *
     * @return $this
     * @throws \Parable\ORM\Exception
     */
    public function setConfig(array $config)
    {
        foreach ($config as $type => $value) {
            $method = 'set' . ucfirst($type);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new \Parable\ORM\Exception(
                    "Tried to set non-existing config value '{$type}' on " . get_class($this)
                );
            }
        }
        return $this;
    }

    /**
     * Prevent leaking sensitive information.
     */
    public function __debugInfo()
    {
    }
}
