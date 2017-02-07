<?php

namespace Parable\ORM;

class Query
{
    /**
     * Join types
     */
    const JOIN_INNER      = 1;
    const JOIN_LEFT       = 2;
    const JOIN_RIGHT      = 3;
    const JOIN_FULL       = 4;

    /** @var \Parable\ORM\Query\Condition[] */
    protected $where      = [];

    /** @var array */
    protected $values     = [];

    /** @var array */
    protected $orderBy    = [];

    /** @var array */
    protected $groupBy    = [];

    /** @var array */
    protected $select     = ['*'];

    /** @var string */
    protected $action     = 'select';

    /** @var \Parable\ORM\Query\Condition[][] */
    protected $joins = [
        self::JOIN_INNER => [],
        self::JOIN_LEFT  => [],
        self::JOIN_RIGHT => [],
        self::JOIN_FULL  => [],
    ];

    /** @var null|string */
    protected $tableName;

    /** @var null|string */
    protected $tableKey;

    /** @var null|int */
    protected $limit;

    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var array */
    protected $acceptedValues = ['select', 'insert', 'update', 'delete'];

    /** @var array */
    protected $nonQuoteStrings = ['*', 'sum', 'max', 'min', 'count', 'avg'];

    /**
     * @param Database $database
     */
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
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get the currently set tableName
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get the currently set tableName, quoted
     *
     * @return null|string
     */
    public function getQuotedTableName()
    {
        return $this->quoteIdentifier($this->tableName);
    }

    /**
     * Set the tableKey to work with (for delete & update)
     *
     * @param string $key
     *
     * @return $this
     */
    public function setTableKey($key)
    {
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
    public function setAction($action)
    {
        if (in_array($action, $this->acceptedValues)) {
            $this->action = $action;
        }
        return $this;
    }

    /**
     * Return the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * In case of a select, what we're going to select (default *)
     *
     * @param array $select
     *
     * @return $this
     */
    public function select(array $select)
    {
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
    public function where($key, $comparator, $value = null)
    {
        /** @var \Parable\ORM\Query\Condition $condition */
        $condition = new \Parable\ORM\Query\Condition();
        $condition
            ->setKey($key)
            ->setComparator($comparator)
            ->setValue($value)
            ->setQuery($this);

        $this->where[] = $condition;
        return $this;
    }

    /**
     * @param int    $type
     * @param string $tableName
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     * @param bool   $shouldCompareFields
     *
     * @return $this
     */
    protected function join(
        $type,
        $tableName,
        $key,
        $comparator,
        $value = null,
        $shouldCompareFields = true
    ) {
        /** @var \Parable\ORM\Query\Condition $condition */
        $condition = new \Parable\ORM\Query\Condition();
        $condition
            ->setTableName($tableName)
            ->setKey($key)
            ->setComparator($comparator)
            ->setValue($value)
            ->setQuery($this)
            ->setShouldCompareFields($shouldCompareFields);

        $this->joins[$type][] = $condition;
        return $this;
    }

    /**
     * @param string $tableName
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     * @param bool   $shouldCompareFields
     *
     * @return $this
     */
    public function innerJoin($tableName, $key, $comparator, $value = null, $shouldCompareFields = true)
    {
        return $this->join(self::JOIN_INNER, $tableName, $key, $comparator, $value, $shouldCompareFields);
    }

    /**
     * @param string $tableName
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     * @param bool   $shouldCompareFields
     *
     * @return $this
     */
    public function leftJoin($tableName, $key, $comparator, $value = null, $shouldCompareFields = true)
    {
        return $this->join(self::JOIN_LEFT, $tableName, $key, $comparator, $value, $shouldCompareFields);
    }

    /**
     * @param string $tableName
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     * @param bool   $shouldCompareFields
     *
     * @return $this
     */
    public function rightJoin($tableName, $key, $comparator, $value = null, $shouldCompareFields = true)
    {
        return $this->join(self::JOIN_RIGHT, $tableName, $key, $comparator, $value, $shouldCompareFields);
    }

    /**
     * @param string $tableName
     * @param string $key
     * @param string $comparator
     * @param mixed  $value
     * @param bool   $shouldCompareFields
     *
     * @return $this
     */
    public function fullJoin($tableName, $key, $comparator, $value = null, $shouldCompareFields = true)
    {
        return $this->join(self::JOIN_FULL, $tableName, $key, $comparator, $value, $shouldCompareFields);
    }

    /**
     * Adds a value to update/insert queries
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addValue($key, $value)
    {
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
    public function orderBy($key, $direction = 'DESC')
    {
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
    public function groupBy($key)
    {
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
    public function limit($limit, $offset = null)
    {
        $this->limit = ['limit' => $limit, 'offset' => $offset];
        return $this;
    }

    /**
     * @return Query
     */
    public static function createInstance()
    {
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
    protected function buildCondition($conditionArray)
    {
        // Check for IN/NOT IN
        if (in_array(strtolower($conditionArray['comparator']), ['in', 'not in'])) {
            $values = $conditionArray['value'];
            $valueArray = [];
            foreach ($values as $value) {
                $valueArray[] = $this->quote($value);
            }
            $conditionArray['value'] = '(' . implode(',', $valueArray) . ')';
        } else {
            $conditionArray['value'] = $this->quote($conditionArray['value']);
        }

        // Check for IS/IS NOT
        if (in_array(strtolower($conditionArray['comparator']), ['is', 'is not'])) {
            $conditionArray['value'] = 'NULL';
        }

        $returnArray = [
            $this->quoteIdentifier($conditionArray['key']),
            $conditionArray['comparator'],
            $conditionArray['value']
        ];

        return implode(' ', $returnArray);
    }

    /**
     * @return string
     */
    protected function buildSelect()
    {
        if (count($this->select) > 0) {
            $selects = [];
            foreach ($this->select as $select) {
                $shouldBeQuoted = true;

                // Check our list of nonQuoteStrings to see if we should quote or not.
                foreach ($this->nonQuoteStrings as $nonQuoteString) {
                    if (strpos(strtolower($select), $nonQuoteString) !== false) {
                        $shouldBeQuoted = false;
                        break;
                    }
                }

                if ($shouldBeQuoted) {
                    $selects[] = $this->getQuotedTableName() . '.' . $this->quoteIdentifier($select);
                } else {
                    $selects[] = $select;
                }
            }
            return implode(', ', $selects);
        }
        return '';
    }

    /**
     * Build JOIN string if they're available
     *
     * @return string
     */
    protected function buildJoins()
    {
        $builtJoins = [];
        foreach ($this->joins as $type => $joins) {
            if (count($joins) > 0) {
                foreach ($joins as $join) {
                    if ($type == self::JOIN_INNER) {
                        $builtJoins[] = "INNER JOIN";
                    } elseif ($type == self::JOIN_LEFT) {
                        $builtJoins[] = "LEFT JOIN";
                    } elseif ($type == self::JOIN_RIGHT) {
                        $builtJoins[] = "RIGHT JOIN";
                    } elseif ($type == self::JOIN_FULL) {
                        $builtJoins[] = "FULL JOIN";
                    }

                    $builtJoins[] = $this->quoteIdentifier($join->getTableName()) . " ON";
                    $builtJoins[] = $join->build();
                }
            }
        }

        return implode(" ", $builtJoins);
    }

    /**
     * Build WHERE string if they're available
     *
     * @return string
     */
    protected function buildWheres()
    {
        if (count($this->where) > 0) {
            $wheres = [];
            foreach ($this->where as $where) {
                $wheres[] = $where->build();
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
    protected function buildOrderBy()
    {
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
    protected function buildGroupBy()
    {
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
    protected function buildLimitOffset()
    {
        if (is_array($this->limit)) {
            $limit = $this->limit['limit'];
            if ($this->limit['offset'] !== null) {
                $limit = $this->limit['offset'] . ',' . $limit;
            }
            $limit = "LIMIT " . $limit;
            return $limit;
        }
        return '';
    }

    /**
     * @param string$string
     *
     * @return string
     */
    public function quote($string)
    {
        if (!$this->database->getInstance()) {
            $string = str_replace("'", "", $string);
            return "'{$string}'";
        }
        return $this->database->quote($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function quoteIdentifier($string)
    {
        if (!$this->database->getInstance()) {
            return "`{$string}`";
        }
        return $this->database->quoteIdentifier($string);
    }

    /**
     * Outputs the actual query for use, empty string if invalid/incomplete values given
     *
     * @return string
     */
    public function __toString()
    {
        $query = [];

        if ($this->action === 'select') {
            $query[] = "SELECT " . $this->buildSelect();
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
                            $correctValue = $this->quote($value);
                        }
                        // Quote the key
                        $key = $this->quoteIdentifier($key);

                        // Add key & value combo to the array
                        $values[] = $key . " = " . $correctValue;
                    } else {
                        $tableKey = $key;
                        $tableKeyValue = $value;
                    }
                }
                $query[] = "SET " . implode(', ', $values);
                $query[] = "WHERE " . $this->quoteIdentifier($tableKey);
                $query[] = " = " . $this->quote($tableKeyValue);
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
                    $keys[] = $this->quoteIdentifier($key);

                    if ($value === null) {
                        $correctValue = 'NULL';
                    } else {
                        $correctValue = $this->quote($value);
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
