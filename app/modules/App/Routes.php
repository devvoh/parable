<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App;

class Routes extends \Devvoh\Parable\Routes {

    /**
     * This function is automatically called when the routes are being loaded
     */
    public function run() {
        $routes = [
            'index' => [
                'method' => 'GET',
                'path' => '/',
                'controller' => 'Home',
                'action' => 'index',
            ],
            'closure' => [
                'method' => 'GET',
                'path' => '/closure',
                'closure' => function() {
                    return 'this is a closure';
                },
            ],
        ];
        $this->registerRoutes($routes, __DIR__);
    }

}

