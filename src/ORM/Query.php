<?php
/**
 * @package     Parable ORM
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\ORM;

class Query {

    /** @var array */
    protected $where    = [];

    /** @var array */
    protected $values   = [];

    /** @var array */
    protected $orderBy  = [];

    /** @var array */
    protected $groupBy  = [];

    /** @var string */
    protected $select   = '*';

    /** @var string */
    protected $action   = 'select';

    /** @var array */
    protected $joins    = [];

    /** @var null|string */
    protected $tableName;

    /** @var null|string */
    protected $tableKey;

    /** @var null|int */
    protected $limit;

    /** @var \Parable\ORM\Database */
    protected $database;

    public function __construct(
        \Parable\ORM\Database $database
    ) {
        $this->database = $database;
    }

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
     * Get the currently set tableName, quoted
     *
     * @return null|string
     */
    public function getQuotedTableName() {
        return $this->database->quoteIdentifier($this->tableName);
    }

    /**
     * Set the tableKey to work with (for delete & update)
     *
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
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     *
     * @return $this
     */
    public function where($key, $comparator, $value = null) {
        $this->where[] = ['key' => $key, 'comparator' => $comparator, 'value' => $value];
        return $this;
    }

    /**
     * Adds a simple join clause
     *
     * @param string $table
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     *
     * @return $this
     */
    public function join($table, $key, $comparator, $value = null) {
        $this->joins[] = ['table' => $table, 'key' => $key, 'comparator' => $comparator, 'value' => $value];
        return $this;
    }

    /**
     * Adds a value to update/insert queries
     *
     * @param string $key
     * @param mixed  $value
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
     * @param int      $limit
     * @param null|int $offset
     *
     * @return $this
     */
    public function limit($limit, $offset = null) {
        $this->limit = ['limit' => $limit, 'offset' => $offset];
        return $this;
    }

    /**
     * @return Query
     */
    public static function createInstance() {
        return \Parable\DI\Container::create(static::class);
    }

    /**
     * Build a condition string from an array of values. Required keys: key, comparator, value.
     *
     * Examples: ['id', '=', 1]
     *           ['id', 'NOT IN', [1, 2, 3, 4]]
     *
     * @param $conditionArray
     *
     * @return string
     */
    protected function buildCondition($conditionArray) {
        // Check for IN/NOT IN
        if (in_array(strtolower($conditionArray['comparator']), ['in', 'not in'])) {
            $values = $conditionArray['value'];
            $valueArray = [];
            foreach ($values as $value) {
                $valueArray[] = $this->database->quote($value);
            }
            $conditionArray['value'] = '(' . implode(',', $valueArray) . ')';
        } else {
            $conditionArray['value'] = $this->database->quote($conditionArray['value']);
        }

        // Check for IS/IS NOT
        if (in_array(strtolower($conditionArray['comparator']), ['is', 'is not'])) {
            $conditionArray['value'] = 'NULL';
        }

        $returnArray = [
            $this->database->quoteIdentifier($conditionArray['key']),
            $conditionArray['comparator'],
            $conditionArray['value']
        ];

        return implode(' ', $returnArray);
    }

    /**
     * Build JOIN string if they're available
     *
     * @return string
     */
    protected function buildJoins() {
        if (count($this->joins) > 0) {
            $joins = [];
            foreach ($this->joins as $join) {
                $joins[] = "JOIN " . $this->database->quoteIdentifier($join['table']) . " ON ";
                $joins[] = $this->buildCondition($join);
            }
            return implode(' ', $joins);
        }
        return '';
    }

    /**
     * Build WHERE string if they're available
     *
     * @return string
     */
    protected function buildWheres() {
        if (count($this->where) > 0) {
            $wheres = [];
            foreach ($this->where as $where) {
                $wheres[] = $this->buildCondition($where);
            }
            return "WHERE " . implode(' AND ', $wheres);
        }
        return '';
    }

    /**
     * Build ORDER BY string if it's available
     *
     * @return string
     */
    protected function buildOrderBy() {
        if (count($this->orderBy) > 0) {
            $orders = [];
            foreach ($this->orderBy as $orderBy) {
                $orders[] = $orderBy['key'] . ' ' . $orderBy['direction'];
            }
            return "ORDER BY " . implode(', ', $orders);
        }
        return '';
    }

    /**
     * Build GROUP BY string if it's available
     *
     * @return string
     */
    protected function buildGroupBy() {
        if (count($this->groupBy) > 0) {
            $groups = [];
            foreach ($this->groupBy as $groupBy) {
                $groups[] = $groupBy;
            }
            return "GROUP BY " . implode(', ', $groups);
        }
        return '';
    }

    /**
     * Build LIMIT/OFFSET string if it's available
     *
     * @return string
     */
    protected function buildLimitOffset() {
        if (is_array($this->limit)) {
            $limit = "LIMIT " . $this->limit['limit'];
            if ($this->limit['offset'] !== null) {
                $limit .= ", " . $this->limit['limit'];
            }
            return $limit;
        }
        return '';
    }

    /**
     * Outputs the actual query for use, empty string if invalid/incomplete values given
     *
     * @return string
     */
    public function __toString() {
        // If there's no valid PDO instance, we can't quote so no query for you
        if (!$this->database->getInstance()) {
            return '';
        }

        $query = [];

        if ($this->action === 'select') {

            $query[] = "SELECT " . $this->select;
            $query[] = "FROM " . $this->getQuotedTableName();
            $query[] = $this->buildJoins();
            $query[] = $this->buildWheres();
            $query[] = $this->buildOrderBy();
            $query[] = $this->buildGroupBy();
            $query[] = $this->buildLimitOffset();

        } elseif ($this->action === 'delete') {

            $query[] = "DELETE FROM " . $this->getQuotedTableName();
            $query[] = $this->buildWheres();

        } elseif ($this->action === 'update') {

            $query[] = "UPDATE " . $this->getQuotedTableName();

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
                            $correctValue = $this->database->quote($value);
                        }
                        // Quote the key
                        $key = $this->database->quoteIdentifier($key);

                        // Add key & value combo to the array
                        $values[] = $key . " = " . $correctValue;
                    } else {
                        $tableKey = $key;
                        $tableKeyValue = $value;
                    }
                }
                $query[] = "SET " . implode(', ', $values);
                $query[] = "WHERE " . $this->database->quoteIdentifier($tableKey) . " = " . $this->database->quote($tableKeyValue);
            } else {
                $query = [];
            }

        } elseif ($this->action === 'insert') {

            // set insert to the proper table
            $query[] = "INSERT INTO " . $this->getQuotedTableName();

            // now get the values
            if (count($this->values) > 0) {

                $keys = [];
                $values = [];
                foreach ($this->values as $key => $value) {
                    // Quote the key
                    $keys[] = $this->database->quoteIdentifier($key);

                    if ($value === null) {
                        $correctValue = 'NULL';
                    } else {
                        $correctValue = $this->database->quote($value);
                    }
                    $values[] = $correctValue;
                }

                $query[] = "(" . implode(', ', $keys) . ")";
                $query[] = "VALUES";
                $query[] = "(" . implode(', ', $values) . ")";
            } else {
                $query = [];
            }

        }

        // and now implode it into a nice string, if possible
        if (count($query) == 0) {
            return '';
        }

        // Now make it nice.
        $queryString = implode(' ', $query);
        $queryString = trim($queryString) . ';';

        // Since we got here, we've got a query to output
        return $queryString;
    }

}
