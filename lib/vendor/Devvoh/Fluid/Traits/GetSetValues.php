<?php
/**
 * @package     Fluid
 * @subpackage  Traits/GetSetId
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\Traits;

trait GetSetValues {

    /**
     * Returns a single value from the parent entity or false
     *
     * @param $key
     *
     * @return bool
     */
    public function getValue($key) {
        $values = $this->getResource();

        if (isset($values[$key])) {
            return $values[$key];
        }

        return false;
    }

    /**
     * Sets a single key/value on the parent entity
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setValue($key, $value) {
        $values = $this->getResource();
        $values[$key] = $value;
        $this->setResource($values);

        return $this;
    }

    /**
     * Get the entire parent entity
     *
     * @return mixed
     */
    public function getValues() {
        return $this->getResource();
    }

    /**
     * Overwrite entire parent entity
     *
     * @param $values
     *
     * @return $this
     */
    public function setValues($values) {
        $this->setResource($values);

        return $this;
    }

    /**
     * Get resource based on parent entity
     *
     * @return mixed
     */
    protected function getResource() {
        switch($this->getClassName()) {
            case 'Post':
                return $_POST;
            case 'Get':
                return $_GET;
            case 'Session':
                return $_SESSION;
        }
        return $this;
    }

    /**
     * Overwrite entire parent entity based on
     *
     * @param $values
     *
     * @return $this
     */
    protected function setResource($values) {
        switch($this->getClassName()) {
            case 'Post':
                $_POST = $values;
                break;
            case 'Get':
                $_GET = $values;
                break;
            case 'Session':
                $_SESSION = $values;
                break;
        }
        return $this;
    }

}