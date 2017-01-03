<?php

class Routes
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
