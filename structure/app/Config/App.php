<?php

namespace Config;

class App extends \Parable\Framework\Config\Base
{
    /** @var null|int */
    protected $sortOrder = 0;

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
            'database' => [
                'type'     => \Parable\ORM\Database::TYPE_MYSQL,
                'location' => 'localhost',
                'username' => 'username',
                'password' => 'password',
                'database' => 'database',
            ],
            'console' => [
                'commands' => [
                    \Command\HelloWorld::class,
                ]
            ]
        ];
    }
}
