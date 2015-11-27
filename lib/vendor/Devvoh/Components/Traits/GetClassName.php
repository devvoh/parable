<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Traits
 * @subpackage  GetClassName
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components\Traits;

trait GetClassName {

    /**
     * @var null|string
     */
    protected $className = null;

    /**
     * @var null|string
     */
    protected $nameSpace = null;

    /**
     * Returns the class name without the namespace
     *
     * @return string
     */
    public function getClassName() {
        if (!$this->className) {
            $classInfo = explode('\\', get_class($this));
            $this->setClassName(array_pop($classInfo));
        }
        return $this->className;
    }

    /**
     * Set the className
     *
     * @param $className
     *
     * @return $this
     */
    public function setClassName($className) {
        $this->className = $className;
        return $this;
    }

    /**
     * Returns the class name without the namespace
     *
     * @return string
     */
    public function getNameSpace() {
        if (!$this->nameSpace) {
            $nameSpace = explode('\\', get_class($this));
            array_pop($nameSpace);
            $nameSpace = implode('\\', $nameSpace);
            $this->setNameSpace($nameSpace);
        }
        return $this->nameSpace;
    }

    /**
     * Set the nameSpace
     *
     * @param $nameSpace
     *
     * @return $this
     */
    public function setNameSpace($nameSpace) {
        $this->nameSpace = $nameSpace;
        return $this;
    }

}