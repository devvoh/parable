<?php

namespace Parable\Tests;

abstract class Base extends \PHPUnit\Framework\TestCase
{
    /**
     * Set $value on $object->$propertyName, even if it's private
     * or protected.
     *
     * @param object $object
     * @param string $propertyName
     * @param mixed  $value
     */
    public function mockProperty($object, $propertyName, $value)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);
    }

    /**
     * Makes the $propertyName publicly accessible.
     *
     * @param object $object
     * @param string $propertyName
     */
    public function liberateProperty($object, $propertyName)
    {
        $reflectionClass = new \ReflectionClass($object);

        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
    }

    /**
     * Make all propertNames passed publicly accessible.
     *
     * @param object   $object
     * @param string[] $propertyNames
     */
    public function liberateProperties($object, array $propertyNames)
    {
        foreach ($propertyNames as $propertyName) {
            $this->liberateProperty($object, $propertyName);
        }
    }

    /**
     * Returns the actual output form the default PHPUnit output buffer,
     * and cleans 1(!) level, clearing the most recent buffer level.
     *
     * @return string
     */
    public function getActualOutputAndClean()
    {
        $content = parent::getActualOutput();
        ob_clean();
        return $content;
    }

    /**
     * @return array
     */
    public function dpTrueFalse()
    {
        return [
            [true],
            [false],
        ];
    }
}
