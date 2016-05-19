<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class DI {

    /**
     * Instantiate a class and fulfill its dependency requirements
     *
     * @param $className
     * @param null $requiredBy
     * @return mixed
     * @throws \Exception
     */
    public function instantiate($className, $requiredBy = null) {
        if (!class_exists($className)) {
            $message = 'Could not create instance of "' . $className . '"';
            if ($requiredBy) {
                $message .= ', required by "' . $requiredBy . '"';
            }
            throw new \Exception($message);
        }
        $reflection = new \ReflectionClass($className);
        /** @var \ReflectionMethod $construct */
        $construct = $reflection->getConstructor();

        if (!$construct) {
            return new $className();
        }

        /** @var \ReflectionParameter[] $parameters */
        $parameters = $construct->getParameters();

        $dpndcClasses = [];
        foreach ($parameters as $parameter) {
            $subClassName = $parameter->name;
            if ($parameter->getClass()) {
                $subClassName = $parameter->getClass()->name;
            }
            $dpndcClasses[] = $this->instantiate($subClassName, $className);
        }
        return (new \ReflectionClass($className))->newInstanceArgs($dpndcClasses);
    }

}
