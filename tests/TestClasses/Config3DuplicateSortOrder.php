<?php

namespace Parable\Tests\TestClasses;

class Config3DuplicateSortOrder extends \Parable\Framework\Config\Base
{
    /** @var null|int */
    protected $sortOrder = 0;

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            'what' => 'do you expect to happen?',
        ];
    }
}
