<?php
/**
 * @package     Fluid
 * @subpackage  Traits/GetSetId
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\Traits;

trait GetSetId {

    protected $id = null;

    /**
     * Returns the id
     *
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the id
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id = null) {
        $this->id = $id;
        return $this;
    }

}