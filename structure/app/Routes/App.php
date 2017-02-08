<?php

namespace Routes;

class App extends \Parable\Framework\Routes\Base
{
    public function get()
    {
        return [
            'index' => [
                'methods' => 'GET',
                'url' => '/',
                'controller' => \Controller\Home::class,
                'action' => 'index',
            ],
        ];
    }
}
