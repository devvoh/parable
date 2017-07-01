<?php

namespace Parable\Tests\TestClasses;

class Config2 extends \Parable\Framework\Config\Base
{
    /** @var null|int */
    protected $sortOrder = 1;

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            'setting' => 'secondary value',
            'also'    => 'this one',
        ];
    }
}
