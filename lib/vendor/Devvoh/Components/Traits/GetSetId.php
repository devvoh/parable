<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Traits
 * @subpackage  GetSetId
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components\Traits;

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