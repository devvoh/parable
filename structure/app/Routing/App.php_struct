<?php

namespace Routing;

class App implements
    \Parable\Framework\Interfaces\Routing
{
    public function get()
    {
        return [
            /*
             * Valid method values are GET, POST, PUT, DELETE, PATCH. If the route is requested
             * through a method it does not support, a 404 is thrown.
             */
            'index' => [
                'methods' => ['GET'],
                'url' => '/',
                'controller' => \Controller\Home::class,
                'action' => 'index',
            ],
            /*
             * If you add parameters to a URL by adding {param} anywhere, it is REQUIRED. Leaving
             * the parameter off in the requested URL will cause a 404 to be thrown.
             *
             * Within a URL, you may specify what type a parameter is required to be.
             *
             * Valid values: {param:int}, {param:string}, {param:float}
             *
             * If the value passed is invalid, a 404 is thrown.
             */
            'test-parameters' => [
                'methods' => ['GET'],
                'url' => '/test/{id:int}/{name}',
                'controller' => \Controller\Home::class,
                'action' => 'test',
            ],
        ];
    }
}
