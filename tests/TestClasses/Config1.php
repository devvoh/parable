<?php

namespace Parable\Tests\TestClasses;

class Config1 implements
    \Parable\Framework\Interfaces\Config
{
    /**
     * @return array
     */
    public function get()
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
