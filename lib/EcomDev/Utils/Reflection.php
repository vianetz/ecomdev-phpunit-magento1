<?php
/**
 * PHP Unit test suite for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   EcomDev
 * @package    EcomDev_PHPUnit
 * @copyright  Copyright (c) 2013 EcomDev BV (http://www.ecomdev.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Ivan Chepurnyi <ivan.chepurnyi@ecomdev.org>
 */

class EcomDev_Utils_Reflection
{
    /**
     * Cache of reflection objects
     *
     * @var array
     */
    protected static $_reflectionCache = array();

    /**
     * Sets protected or private property value
     *
     * @param string|object $object class name
     * @param string $property
     * @param mixed $value
     * @throws RuntimeException
     */
    public static function setRestrictedPropertyValue($object, $property, $value)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            throw new RuntimeException('For setting of restricted properties via Reflection, PHP version should be 5.3.0 or later');
        }

        $reflectionObject = self::getReflection($object);
        while (!$reflectionObject->hasProperty($property)) {
            if (!$reflectionObject->getParentClass()) {
                break;
            }
            $reflectionObject = $reflectionObject->getParentClass();
        }

        $reflectionProperty = $reflectionObject->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue((is_string($object) ? null : $object), $value);
    }

    /**
     * Sets multiple restricted property values for an object
     *
     * @param string|object $object class name
     * @param array $properties
     * @throws RuntimeException
     */
    public static function setRestrictedPropertyValues($object, array $properties)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            throw new RuntimeException('For setting of restricted properties via Reflection, PHP version should be 5.3.0 or later');
        }

        foreach ($properties as $property => $value) {
            self::setRestrictedPropertyValue($object, $property, $value);
        }
    }

    /**
     * Gets protected or private property value
     *
     * @param string|object $object class name
     * @param string $property
     * @return mixed
     * @throws RuntimeException
     */
    public static function getRestrictedPropertyValue($object, $property)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            throw new RuntimeException('For getting of restricted properties via Reflection, PHP version should be 5.3.0 or later');
        }

        $reflectionObject = self::getReflection($object);
        
        while (!$reflectionObject->hasProperty($property)) {
            if ($reflectionObject->getParentClass()) {
                $reflectionObject = $reflectionObject->getParentClass();
            } else {
                break;
            }
        }
        
        $reflectionProperty = $reflectionObject->getProperty($property);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue((is_string($object) ? null : $object));
    }


    /**
     * Calls private or protected method
     *
     * @param string|object $object
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws RuntimeException
     */
    public static function invokeRestrictedMethod($object, $method, $args = array())
    {
        if (version_compare(PHP_VERSION, '5.3.2', '<')) {
            throw new RuntimeException('For invoking restricted methods via Reflection, PHP version should be 5.3.2 or later');
        }

        $reflectionObject = self::getReflection($object);
        $reflectionMethod = $reflectionObject->getMethod($method);
        $reflectionMethod->setAccessible(true);

        if (!empty($args)) {
            return $reflectionMethod->invokeArgs((is_string($object) ? null : $object), $args);
        }

        return $reflectionMethod->invoke((is_string($object) ? null : $object));
    }

    /**
     * Returns reflection object from instance or class name
     *
     * @param string|object $object
     * @return ReflectionClass|ReflectionObject
     * @deprecated since 0.3.0 deprecated because of typo
     */
    public static function getRelflection($object)
    {
        return self::getReflection($object);
    }

    /**
     * Returns reflection object from instance or class name
     *
     * @param string|object $object
     * @return ReflectionClass|ReflectionObject
     * @throws InvalidArgumentException
     */
    public static function getReflection($object)
    {
        // If object is a class name
        if (is_string($object) && class_exists($object)) {
            if (isset(self::$_reflectionCache[$object])) {
                return self::$_reflectionCache[$object];
            }
            $reflection = new ReflectionClass($object);
            self::$_reflectionCache[$object] = $reflection;
            return $reflection;
        }
        // If object is an instance of a class
        elseif (is_object($object)) {
            $objectHash = spl_object_hash($object);
            if (isset(self::$_reflectionCache[$objectHash])) {
                return self::$_reflectionCache[$objectHash];
            }
            $reflection = new ReflectionObject($object);
            self::$_reflectionCache[$objectHash] = $reflection;
            return $reflection;
        }
        // In case of invalid argument
        else {
            throw new InvalidArgumentException("$object should be a valid class name or object instance");
        }
    }

    /**
     * Creates a new instance of object, without calling original constructor
     * 
     * @param string $className
     * @param array $properties
     * @return object
     */
    public static function createObject($className, array $properties = array())
    {
        $reflection = self::getReflection($className);
        $object = $reflection->newInstanceWithoutConstructor();
        self::setRestrictedPropertyValues($object, $properties);
        return $object;
    }
}