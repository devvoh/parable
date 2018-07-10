<?php

namespace Parable\ORM\Query;

use Parable\ORM\Query;

abstract class ConditionSet
{
    /** Types of condition sets */
    const SET_AND = 'AND';
    const SET_OR  = 'OR';

    /** Default condition set is AND */
    const TYPE    = self::SET_AND;

    /** @var Query */
    protected $query;

    /** @var Condition[] */
    protected $conditions = [];

    public function __construct(
        Query $query,
        array $conditions
    ) {
        $this->query = $query;

        if (!is_array($conditions[0]) && !is_object($conditions[0])) {
            $conditions = [$conditions];
        }

        foreach ($conditions as $condition) {
            if (!is_array($condition)) {
                $this->conditions[] = $condition;
                continue;
            }
            // Build a new condition
            $conditionObject = new Condition();
            $conditionObject->setQuery($this->query);

            if (isset($condition[0])) {
                $conditionObject->setKey($condition[0]);
            }
            if (isset($condition[1])) {
                $conditionObject->setComparator($condition[1]);
            }
            if (isset($condition[2])) {
                $conditionObject->setValue($condition[2]);
            }
            if (isset($condition[3])) {
                $conditionObject->setTableName($condition[3]);
            }
            $this->conditions[] = $conditionObject;
        }
    }

    /**
     * Build the conditions into a string. Can be done either with parentheses or without.
     *
     * @param bool $withParentheses
     *
     * @return string
     */
    protected function build($withParentheses)
    {
        $builtConditions = [];
        foreach ($this->conditions as $condition) {
            if ($condition instanceof \Parable\ORM\Query\ConditionSet) {
                $builtConditions[] = $condition->buildWithParentheses();
            } else {
                $builtConditions[] = $condition->build();
            }
        }

        $glue = ' ' . static::TYPE . ' ';

        $string = implode($glue, $builtConditions);
        if ($withParentheses) {
            $string = "({$string})";
        }

        return $string;
    }

    /**
     * Build the conditions into a string without parentheses.
     *
     * @return string
     */
    public function buildWithoutParentheses()
    {
        return $this->build(false);
    }

    /**
     * Build the conditions into a string with parentheses.
     *
     * @return string
     */
    public function buildWithParentheses()
    {
        return $this->build(true);
    }
}
