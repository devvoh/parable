<?php
/**
 * @package     Fluid
 * @subpackage  Query
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid;

use Devvoh\Fluid\App as App;

class Query {

    protected $tableName    = null;
    protected $tableKey     = null;
    protected $where        = array();
    protected $values       = array();
    protected $orderBy      = array();
    protected $groupBy      = array();
    protected $limit        = null;

    protected $select       = '*';
    protected $action       = 'select';

    /**
     * Set the tableName to work on
     *
     * @param string $tableName
     * @return Query
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
     * Set the tableKey to work with (for delete & update
     * )
     * @param string $key
     * @return Query
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
     * @return Query
     */
    public function setAction($action) {
        if (in_array($action, array('select', 'insert', 'delete', 'update'))) {
            $this->action = $action;
        }

        return $this;
    }

    /**
     * In case of a select, what we're going to select (default *)
     *
     * @param string $select
     * @return Query
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
     * @return Query
     */
    public function where($condition, $value = null) {
        $this->where[] = array('condition' => $condition, 'value' => $value);

        return $this;
    }

    /**
     * Adds a value to update/insert queries
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Query
     */
    public function addValue($key, $value) {
        $this->values[] = array('key' => $key, 'value' => $value);

        return $this;
    }

    /**
     * Sets the order for select queries
     *
     * @param string $key
     * @param string $direction (default DESC)
     *
     * @return Query
     */
    public function orderBy($key, $direction = 'DESC') {
        $this->orderBy[] = array('key' => $key, 'direction' => $direction);

        return $this;
    }

    /**
     * Sets the group by for select queries
     *
     * @param string $key
     *
     * @return Query
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
     * @return Query
     */
    public function limit($limit, $offset = null) {
        $this->limit = array('limit' => $limit, 'offset' => $offset);

        return $this;
    }

    /**
     * Outputs the actual query for use, empty string if invalid/incomplete values given
     *
     * @return string
     */
    public function __toString() {
        // If there's no valid DB instance, we can't quote so no query for you
        if (!App::getDatabase()->getInstance()) {
            return false;
        }

        $query = array();

        if ($this->action === 'select') {

            // set select & what needs to be selected
            $query[] = "SELECT " . $this->select;
            // table
            $query[] = "FROM " . $this->tableName;

            // now get the where clauses
            if (count($this->where) > 0) {
                $wheres = array();
                foreach ($this->where as $where) {
                    if ($where['value'] !== null) {
                        $wheres[] = str_replace('?', App::getDatabase()->quote($where['value']), $where['condition']);
                    } else {
                        $wheres[] = $where['condition'];
                    }
                }
                $query[] = "WHERE " . implode(' AND ', $wheres);
            }

            // now get the order(s)
            if (count($this->orderBy) > 0) {
                $orders = array();
                foreach ($this->orderBy as $orderBy) {
                    $orders[] = $orderBy['key'] . ' ' . $orderBy['direction'];
                }
                $query[] = "ORDER BY " . implode(', ', $orders);
            }

            // now get the group(s)
            if (count($this->groupBy) > 0) {
                $groups = array();
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
                $wheres = array();
                foreach ($this->where as $where) {
                    if ($where['value'] !== null) {
                        $wheres[] = str_replace('?', App::getDatabase()->quote($where['value']), $where['condition']);
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
                $values = array();
                foreach ($this->values as $value) {
                    // skip id, since we'll use that as a where condition
                    if ($value['key'] !== $this->tableKey) {
                        $values[] = "'" . $value['key'] . "'=" . App::getDatabase()->quote($value['value']);
                    } else {
                        $tableKey = $value['key'];
                        $tableKeyValue = $value['value'];
                    }
                }
                $query[] = "SET " . implode(',', $values);
                $query[] = "WHERE " . $tableKey . " = " . App::getDatabase()->quote($tableKeyValue);
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
                    $values[] = App::getDatabase()->quote($value['value']);
                }

                $query[] = "(" . implode(',', $keys) . ")";
                $query[] = "VALUES";
                $query[] = "(" . implode(',', $values) . ")";
            } else {
                $query = [];
            }

        }

        // and now implode it into a nice string, if possible
        if (count($query) > 0) {
            return implode(' ', $query);
        }

        return '';
    }

}