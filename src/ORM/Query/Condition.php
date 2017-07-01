<?php

namespace Parable\ORM\Query;

class Condition
{
    /** @var \Parable\ORM\Query */
    protected $query;

    /** @var string */
    protected $tableName;

    /** @var string */
    protected $joinTableName;

    /** @var string */
    protected $key;

    /** @var string */
    protected $comparator = '=';

    /** @var mixed */
    protected $value;

    /** @var bool */
    protected $shouldQuoteValues = true;

    /** @var bool */
    protected $shouldCompareFields = false;

    /**
     * @param \Parable\ORM\Query $query
     *
     * @return $this
     */
    public function setQuery(\Parable\ORM\Query $query)
    {
        $this->query = $query;

        $this->setTableName($query->getTableName());
        return $this;
    }

    /**
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
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $joinTableName
     *
     * @return $this
     */
    public function setJoinTableName($joinTableName)
    {
        $this->joinTableName = $joinTableName;
        return $this;
    }

    /**
     * @return string
     */
    public function getJoinTableName()
    {
        return $this->joinTableName;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $comparator
     *
     * @return $this
     */
    public function setComparator($comparator)
    {
        $this->comparator = $comparator;
        return $this;
    }

    /**
     * @return string
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param bool $shouldQuoteValues
     *
     * @return $this
     */
    public function setShouldQuoteValues($shouldQuoteValues)
    {
        $this->shouldQuoteValues = (bool)$shouldQuoteValues;
        return $this;
    }

    /**
     * @return bool
     */
    public function shouldQuoteValues()
    {
        return $this->shouldQuoteValues;
    }

    /**
     * @param bool $shouldCompareFields
     *
     * @return $this
     */
    public function setShouldCompareFields($shouldCompareFields)
    {
        // We need to set shouldQuoteValues to the opposite of shouldCompareFields
        $this->shouldQuoteValues = !$shouldCompareFields;

        $this->shouldCompareFields = (bool)$shouldCompareFields;
        return $this;
    }

    /**
     * @return bool
     */
    protected function shouldCompareFields()
    {
        return $this->shouldCompareFields;
    }

    /**
     * @return bool
     */
    protected function isComparatorInNotIn()
    {
        return in_array(strtolower($this->getComparator()), ['in', 'not in']);
    }

    /**
     * @return bool
     */
    protected function isComparatorIsNotNullIsNull()
    {
        return in_array(strtolower($this->getComparator()), ['is null', 'is not null']);
    }

    /**
     * @return $this
     */
    protected function uppercaseComparator()
    {
        $this->setComparator(strtoupper($this->getComparator()));
        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $value = $this->getValue();

        // Check for IS/IS NOT and set the value to NULL if it is.
        if ($this->isComparatorIsNotNullIsNull()) {
            $this->uppercaseComparator();
            $value = null;
        }

        // Check for IN/NOT IN and build a nice comma-separated list.
        if (!$this->isComparatorIsNotNullIsNull() && $this->isComparatorInNotIn() && is_array($value)) {
            $this->uppercaseComparator();
            $valueArray = [];
            foreach ($value as $valueItem) {
                if ($this->shouldQuoteValues()) {
                    $valueArray[] = $this->query->quote($valueItem);
                } else {
                    $valueArray[] = $valueItem;
                }
            }
            $value = '(' . implode(',', $valueArray) . ')';
        }

        // Now check if we need to still quote the value.
        if (!$this->isComparatorIsNotNullIsNull() && !$this->isComparatorInNotIn() && $this->shouldQuoteValues()) {
            $value = $this->query->quote($value);
        }

        // If we don't have IN/NOT IN, IS/NOT IS, and we shouldn't quote, we assume we're checking fields.
        if ($this->shouldCompareFields()) {
            $valueBuild = [
                $this->query->getQuotedTableName(),
                '.',
                $this->query->quoteIdentifier($value),
            ];
            $value = implode($valueBuild);
        }

        $tableName = $this->getTableName();
        if ($this->getJoinTableName()) {
            $tableName = $this->getJoinTableName();
        }
        $tableName = $this->query->quoteIdentifier($tableName);

        $returnArray = [
            $tableName . '.' . $this->query->quoteIdentifier($this->getKey()),
            $this->getComparator(),
            $value,
        ];
        $returnString = implode(' ', $returnArray);

        return trim($returnString);
    }
}
