<?php

namespace Parable\Framework\Routes;

abstract class Base
{
    /**
     * Returns a (multidimensional) array with routes:
     *
     * return [
     *     'index' => [
     *         'methods' => 'GET',
     *         'url' => '/',
     *         'controller' => \Controller\Home::class,
     *         'action' => 'index',
     *     ],
     * ]
     *
     * @return array
     */
    abstract public function get();
}
