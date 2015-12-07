<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Query
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Query {
    use \Devvoh\Components\Traits\GetClassName;

    protected $tableName    = null;
    protected $tableKey     = null;
    protected $limit        = null;
    protected $pdoInstance  = null;
    protected $where        = [];
    protected $values       = [];
    protected $orderBy      = [];
    protected $groupBy      = [];
    protected $select       = '*';
    protected $action       = 'select';

    /**
     * Set the tableName to work on
     *
     * @param string $tableName
     * @return \Devvoh\Components\Query
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
     * @return \Devvoh\Components\Query
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
     * @return \Devvoh\Components\Query
     */
    public function setTableKey($key) {
        $this->tableKey = $key;
        return $this;
    }

    /**
     * Set the type of query we're going to do
     *
     * @param string $action (select, insert, delete, update)
     *
     * @return \Devvoh\Components\Query
     */
    public function setAction($action) {
        if (in_array($action, ['select', 'insert', 'delete', 'update'])) {
            $this->action = $action;
        }
        return $this;
    }

    /**
     * In case of a select, what we're going to select (default *)
     *
     * @param string $select
     * @return \Devvoh\Components\Query
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
     * @return \Devvoh\Components\Query
     */
    public function where($condition, $value = null) {
        $this->where[] = ['condition' => $condition, 'value' => $value];
        return $this;
    }

    /**
     * Adds a value to update/insert queries
     *
     * @param string $key
     * @param mixed $value
     *
     * @return \Devvoh\Components\Query
     */
    public function addValue($key, $value) {
        $this->values[] = ['key' => $key, 'value' => $value];
        return $this;
    }

    /**
     * Sets the order for select queries
     *
     * @param string $key
     * @param string $direction (default DESC)
     *
     * @return \Devvoh\Components\Query
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
     * @return \Devvoh\Components\Query
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
     * @return \Devvoh\Components\Query
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
            $query[] = "FROM " . $this->tableName;

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
            $query[] = "DELETE FROM " . $this->tableName;

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
            } else {
                $query = [];
            }

        } elseif ($this->action === 'update') {

            // set update to the proper table
            $query[] = "UPDATE " . $this->tableName;

            // now get the values
            if (count($this->values) > 0) {
                $values = [];
                foreach ($this->values as $value) {
                    // skip id, since we'll use that as a where condition
                    if ($value['key'] !== $this->tableKey) {
                        $values[] = "'" . $value['key'] . "'=" . $this->pdoInstance->quote($value['value']);
                    } else {
                        $tableKey = $value['key'];
                        $tableKeyValue = $value['value'];
                    }
                }
                $query[] = "SET " . implode(',', $values);
                $query[] = "WHERE " . $tableKey . " = " . $this->pdoInstance->quote($tableKeyValue);
            } else {
                $query = [];
            }

        } elseif ($this->action === 'insert') {

            // set insert to the proper table
            $query[] = "INSERT INTO " . $this->tableName;

            // now get the values
            if (count($this->values) > 0) {
                foreach ($this->values as $value) {
                    $keys[] = "'" . $value['key'] . "'";
                    $values[] = $this->pdoInstance->quote($value['value']);
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