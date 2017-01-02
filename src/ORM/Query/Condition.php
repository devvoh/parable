<?php
/**
 * @package     Parable ORM
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\ORM\Query;

class Condition
{
    /** @var \Parable\ORM\Query */
    protected $query;

    /** @var string */
    protected $tableName;

    /** @var string */
    protected $key;

    /** @var string */
    protected $comparator = '=';

    /** @var mixed */
    protected $value = null;

    /** @var bool */
    protected $shouldQuoteValues = true;

    /** @var bool */
    protected $shouldCompareFields = false;

    public function setQuery(\Parable\ORM\Query $query)
    {
        $this->query = $query;
        return $this;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setComparator($comparator)
    {
        $this->comparator = $comparator;
        return $this;
    }

    public function getComparator()
    {
        return $this->comparator;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setShouldQuoteValues($shouldQuoteValues)
    {
        $this->shouldQuoteValues = (bool)$shouldQuoteValues;
        return $this;
    }

    public function shouldQuoteValues()
    {
        return $this->shouldQuoteValues;
    }

    public function setShouldCompareFields($shouldCompareFields)
    {
        // We need to set shouldQuoteValues to the opposite of shouldCompareFields
        $this->shouldQuoteValues = !$shouldCompareFields;

        $this->shouldCompareFields = (bool)$shouldCompareFields;
        return $this;
    }

    public function shouldCompareFields()
    {
        return $this->shouldCompareFields;
    }

    public function isComparatorInNotIn()
    {
        return in_array(strtolower($this->getComparator()), ['in', 'not in']);
    }

    public function isComparatorIsNotIs()
    {
        return in_array(strtolower($this->getComparator()), ['is', 'is not']);
    }

    public function build()
    {
        // Check for IS/IS NOT and set the value to NULL if it is.
        if ($this->isComparatorIsNotIs() && is_array($this->getValue())) {
            $this->setValue('NULL');
        }

        // Check for IN/NOT IN and build a nice comma-separated list.
        if (!$this->isComparatorIsNotIs() && $this->isComparatorInNotIn() && is_array($this->getValue())) {
            $values = $this->getValue();
            $valueArray = [];
            foreach ($values as $value) {
                if ($this->shouldQuoteValues()) {
                    $valueArray[] = $this->query->quote($value);
                } else {
                    $valueArray[] = $value;
                }
            }
            $this->setValue('(' . implode(',', $valueArray) . ')');
        }

        // Now check if we need to still quote the value.
        if (!$this->isComparatorIsNotIs() && !$this->isComparatorInNotIn() && $this->shouldQuoteValues()) {
            $this->setValue($this->query->quote($this->getValue()));
        }

        // If we don't have IN/NOT IN, IS/NOT IS, and we shouldn't quote, we assume we're checking fields.
        if ($this->shouldCompareFields()) {
            $valueBuild = [
                $this->query->getQuotedTableName(),
                '.',
                $this->query->quoteIdentifier($this->getValue()),
            ];
            $value = implode($valueBuild);
        } else {
            $value = $this->getValue();
        }

        $returnArray = [
            $this->query->quoteIdentifier($this->getTableName()) . '.' . $this->query->quoteIdentifier($this->getKey()),
            $this->getComparator(),
            $value,
        ];

        return implode(' ', $returnArray);
    }
}
