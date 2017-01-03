<?php

namespace Config;

class App implements \Parable\Framework\Interfaces\Config
{
    /** @var null|int */
    protected $sortOrder = 0;

    /**
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return [
            'app' => [
                'title'      => 'Parable'
            ],
            'session' => [
                'autoEnable' => true,
            ],
            'initLocations'  => [
                'app/Init',
            ],
        ];
    }
}
