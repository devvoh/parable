<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class DI {

    protected static $instances = [];

    public static function get($className, $requiredBy = null) {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = self::create($className, $requiredBy);
        }
        return self::$instances[$className];
    }

    /**
     * Instantiate a class and fulfill its dependency requirements
     *
     * @param $className
     * @param null $requiredBy
     * @return mixed
     * @throws \Devvoh\Components\Exception
     */
    public static function create($className, $requiredBy = null) {
        if (!class_exists($className)) {
            $message = 'Could not create instance of "' . $className . '"';
            if ($requiredBy) {
                $message .= ', required by "' . $requiredBy . '"';
            }
            throw new \Devvoh\Components\Exception($message);
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
            $dpndcClasses[] = self::create($subClassName, $className);
        }
        return (new \ReflectionClass($className))->newInstanceArgs($dpndcClasses);
    }

}
