<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Traits
 * @subpackage  MagicGetSet
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components\Traits;

trait MagicGetSet {

    public function __call($method, $args)
    {
        $methodCalled = strtolower(substr($method, 0, 3));
        $property = lcfirst(substr($method, 3));

        if ($methodCalled === 'set') {
            if (property_exists($this, $property)) {
                $this->$property = $args;
            }
            return $this;
        } elseif ($methodCalled === 'get') {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
            return null;
        }
    }

}