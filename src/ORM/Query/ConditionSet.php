<?php

namespace Parable\ORM\Query;

abstract class ConditionSet
{
    const TYPE = 'AND';

    /** @var \Parable\ORM\Query */
    protected $query;

    /** @var \Parable\ORM\Query\Condition[] */
    protected $conditions = [];

    public function __construct(\Parable\ORM\Query $query, array $conditions)
    {
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
            $conditionObject = new \Parable\ORM\Query\Condition();
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
            $this->conditions[] = $conditionObject;
        }
    }

    /**
     * @param bool $withParentheses
     *
     * @return string
     */
    public function build($withParentheses = true)
    {
        $builtConditions = [];
        foreach ($this->conditions as $condition) {
            $builtConditions[] = $condition->build();
        }

        $glue = ' ' . static::TYPE . ' ';

        $string = implode($glue, $builtConditions);
        if ($withParentheses) {
            $string = " ({$string}) ";
        }

        return $string;
    }

    public function buildWithoutParentheses()
    {
        return $this->build(false);
    }
}
