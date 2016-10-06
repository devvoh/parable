<?php
/**
 * @package     Parable DI
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\DI;

class Container
{
    /** @var array */
    protected static $instances = [];

    /** @var array */
    protected static $relations = [];

    /**
     * Get an already instantiated instance or create a new one.
     *
     * @param string $className
     * @param string $parentClassName
     *
     * @return object
     * @throws \Exception
     */
    public static function get($className, $parentClassName = '')
    {
        // We store the relationship between class & parent to prevent cyclical references
        if ($parentClassName) {
            self::$relations[$className][$parentClassName] = true;
        }

        // And we check for cyclical references to prevent infinite loops
        if ($parentClassName
            && isset(self::$relations[$parentClassName])
            && isset(self::$relations[$parentClassName][$className])
        ) {
            $message  = 'Cyclical dependency found: ' . $className . ' depends on ' . $parentClassName;
            $message .= ' but is itself a dependency of ' . $parentClassName . '.';
            throw new \Exception($message);
        }

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = self::create($className, $parentClassName);
        }

        return self::$instances[$className];
    }

    /**
     * Instantiate a class and fulfill its dependency requirements
     *
     * @param string $className
     * @param string $parentClassName
     *
     * @return object
     * @throws \Exception
     */
    public static function create($className, $parentClassName = '')
    {
        if (!class_exists($className)) {
            $message = 'Could not create instance of "' . $className . '"';
            if ($parentClassName) {
                $message .= ', required by "' . $parentClassName . '"';
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

        $dependencies = [];
        foreach ($parameters as $parameter) {
            $subClassName = $parameter->name;
            if ($parameter->getClass()) {
                $subClassName = $parameter->getClass()->name;
            }
            $dependencies[] = self::get($subClassName, $className);
        }
        return (new \ReflectionClass($className))->newInstanceArgs($dependencies);
    }

    /**
     * Store a class under its className
     *
     * @param object $class
     */
    public static function store($class)
    {
        self::$instances[get_class($class)] = $class;
    }
}
