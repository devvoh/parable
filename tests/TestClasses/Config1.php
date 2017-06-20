<?php

namespace Parable\Tests\TestClasses;

class Config1 extends \Parable\Framework\Config\Base
{
    /** @var null|int */
    protected $sortOrder = 0;

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            'setting' => 'primary value',
            'nested' => [
                'values' => [
                    'rock' => [
                        'so' => [
                            'much' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
