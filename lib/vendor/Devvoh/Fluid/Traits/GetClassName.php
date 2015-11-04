<?php
/**
 * @package     Fluid
 * @subpackage  Traits/GetSetId
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\Traits;

trait GetClassName {

    /**
     * Returns the class name without the namespace
     *
     * @return mixed
     */
    public function getClassName() {
        return array_pop(explode('\\', get_class($this)));
    }

    /**
     * Returns the class name without the namespace
     *
     * @return mixed
     */
    public function getNameSpace() {
        $namespace = explode('\\', get_class($this));
        array_pop($namespace);
        return implode('\\', $namespace);
    }

}