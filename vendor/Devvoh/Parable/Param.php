<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Param extends \Devvoh\Components\GetSet {

    /**
     * Set the resource to param
     */
    public function __construct() {
        $this->setResource('param');
    }

}