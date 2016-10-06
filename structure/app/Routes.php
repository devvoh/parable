<?php
/**
 * @package     Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

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
