<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Query {

    /**
     * @var null|string
     */
    protected $tableName    = null;

    /**
     * @var null|string
     */
    protected $tableKey     = null;

    /**
     * @var null|int
     */
    protected $limit        = null;

    /**
     * @var null|\PDO
     */
    protected $pdoInstance  = null;

    /**
     * @var array
     */
    protected $where        = [];

    /**
     * @var array
     */
    protected $values       = [];

    /**
     * @var array
     */
    protected $orderBy      = [];

    /**
     * @var array
     */
    protected $groupBy      = [];

    /**
     * @var string
     */
    protected $select       = '*';

    /**
     * @var string
     */
    protected $action       = 'select';

    /**
     * @var array
     */
    protected $joins        = [];

    /**
     * Set the tableName to work on
     *
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get the currently set tableName
     *
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Set the pdoInstance to work on if it's a PDO instance
     *
     * @param \PDO $pdoInstance
     *
     * @return $this
     */
    public function setPdoInstance($pdoInstance) {
        if ($pdoInstance instanceof \PDO) {
            $this->pdoInstance = $pdoInstance;
        }
        return $this;
    }

    /**
     * Get the currently set pdoInstance
     *
     * @return \PDO
     */
    public function getPdoInstance() {
        return $this->pdoInstance;
    }

    /**
     * Set the tableKey to work with (for delete & update
     * )
     * @param string $key
     *
     * @return $this
     */
    public function setTableKey($key) {
        $this->tableKey = $key;
        return $this;
    }

    /**
     * Set the type of query we're going to do
     *
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action) {
        if (in_array($action, ['select', 'insert', 'delete', 'update'])) {
            $this->action = $action;
        }
        return $this;
    }

    /**
     * Return the action
     *
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * In case of a select, what we're going to select (default *)
     *
     * @param string $select
     *
     * @return $this
     */
    public function select($select) {
        $this->select = $select;
        return $this;
    }

    /**
     * Adds a where condition for relevant queries
     *
     * @param string $condition
     * @param mixed $value
     *
     * @return $this
     */
    public function where($condition, $value = null) {
        $this->where[] = ['condition' => $condition, 'value' => $value];
        return $this;
    }

    /**
     * Adds a simple join clause
     *
     * @param string $table
     * @param string $condition
     * @param mixed $value
     *
     * @return $this
     */
    public function join($table, $condition, $value = null) {
        $this->joins[] = ['table' => $table, 'condition' => $condition, 'value' => $value];
        return $this;
    }

    /**
     * Adds a value to update/insert queries
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function addValue($key, $value) {
        $this->values[$key] = $value;
        return $this;
    }

    /**
     * Sets the order for select queries
     *
     * @param string $key
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($key, $direction = 'DESC') {
        $this->orderBy[] = ['key' => $key, 'direction' => $direction];
        return $this;
    }

    /**
     * Sets the group by for select queries
     *
     * @param string $key
     *
     * @return $this
     */
    public function groupBy($key) {
        $this->groupBy[] = $key;
        return $this;
    }

    /**
     * Sets the limit
     *
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function limit($limit, $offset = null) {
        $this->limit = ['limit' => $limit, 'offset' => $offset];
        return $this;
    }

    /**
     * Outputs the actual query for use, empty string if invalid/incomplete values given
     *
     * @return string
     */
    public function __toString() {
        // If there's no valid PDO instance, we can't quote so no query for you
        if (!$this->getPdoInstance()) {
            return '';
        }

        $query = [];

        if ($this->action === 'select') {

            // set select & what needs to be selected
            $query[] = "SELECT " . $this->select;
            // table
            $query[] = "FROM " . $this->pdoInstance->quote($this->tableName);

            // Add the left joins
            if (count($this->joins) > 0) {
                $joins = [];
                foreach ($this->joins as $join) {
                    $joins[] = "JOIN " . $join['table'];
                    if ($join['value'] !== null) {
                        $joins[] = "ON " . str_replace('?', $this->pdoInstance->quote($join['value']), $join['condition']);
                    } else {
                        $joins[] = "ON " . $join['condition'];
                    }
                }
                $query[] = implode(' ', $joins);
            }

            // now get the where clauses
            if (count($this->where) > 0) {
                $wheres = [];
                foreach ($this->where as $where) {
                    if ($where['value'] !== null) {
                        $wheres[] = str_replace('?', $this->pdoInstance->quote($where['value']), $where['condition']);
                    } else {
                        $wheres[] = $where['condition'];
                    }
                }
                $query[] = "WHERE " . implode(' AND ', $wheres);
            }

            // now get the order(s)
            if (count($this->orderBy) > 0) {
                $orders = [];
                foreach ($this->orderBy as $orderBy) {
                    $orders[] = $orderBy['key'] . ' ' . $orderBy['direction'];
                }
                $query[] = "ORDER BY " . implode(', ', $orders);
            }

            // now get the group(s)
            if (count($this->groupBy) > 0) {
                $groups = [];
                foreach ($this->groupBy as $groupBy) {
                    $groups[] = $groupBy;
                }
                $query[] = "GROUP BY " . implode(', ', $groups);
            }

            // and the limit
            if (is_array($this->limit)) {
                if ($this->limit['offset'] !== null && $this->limit['limit'] !== null) {
                    $query[] = "LIMIT " . $this->limit['offset'] . ", " . $this->limit['limit'];
                } elseif ($this->limit['limit'] !== null) {
                    $query[] = "LIMIT " . $this->limit['limit'];
                }
            }

        } elseif ($this->action === 'delete') {

            // set delete to the proper table
            $query[] = "DELETE FROM " . $this->pdoInstance->quote($this->tableName);

            // now get the where clauses
            if (count($this->where) > 0) {
                $wheres = [];
                foreach ($this->where as $where) {
                    if ($where['value'] !== null) {
                        $wheres[] = str_replace('?', $this->pdoInstance->quote($where['value']), $where['condition']);
                    } else {
                        $wheres[] = $where['condition'];
                    }
                }
                $query[] = "WHERE " . implode(' AND ', $wheres);
            }

        } elseif ($this->action === 'update') {

            // set update to the proper table
            $query[] = "UPDATE " . $this->pdoInstance->quote($this->tableName);

            // now get the values
            if (count($this->values) > 0) {
                // Set the table values to defaults
                $tableKey = 'id';
                $tableKeyValue = null;

                $values = [];
                foreach ($this->values as $key => $value) {
                    // skip id, since we'll use that as a where condition
                    if ($key !== $this->tableKey) {
                        if ($value === null) {
                            $correctValue = 'NULL';
                        } else {
                            $correctValue = $this->pdoInstance->quote($value);
                        }
                        $values[] = "'" . $key . "'=" . $correctValue;
                    } else {
                        $tableKey = $key;
                        $tableKeyValue = $value;
                    }
                }
                $query[] = "SET " . implode(',', $values);
                $query[] = "WHERE " . $tableKey . " = " . $this->pdoInstance->quote($tableKeyValue);
            } else {
                $query = [];
            }

        } elseif ($this->action === 'insert') {

            // set insert to the proper table
            $query[] = "INSERT INTO " . $this->pdoInstance->quote($this->tableName);

            // now get the values
            if (count($this->values) > 0) {

                $keys = [];
                $values = [];
                foreach ($this->values as $key => $value) {
                    $keys[] = "'" . $key . "'";

                    if ($value === null) {
                        $correctValue = 'NULL';
                    } else {
                        $correctValue = $this->pdoInstance->quote($value);
                    }
                    $values[] = $correctValue;
                }

                $query[] = "(" . implode(',', $keys) . ")";
                $query[] = "VALUES";
                $query[] = "(" . implode(',', $values) . ")";
            } else {
                $query = [];
            }

        }

        // and now implode it into a nice string, if possible
        if (count($query) == 0) {
            return '';
        }

        // Since we got here, we've got a query to output
        return implode(' ', $query);
    }

}