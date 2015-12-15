<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  Entity
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid;

use \Devvoh\Fluid\App;

class Entity {
    use \Devvoh\Components\Traits\GetClassName;
    use \Devvoh\Components\Traits\GetSetId;

    public function __call($method, $args) {
        var_dump($method, $args);
    }

    public function save() {
        var_dump('save entity');
    }

    public function delete() {
        var_dump('delete entity');
    }
}