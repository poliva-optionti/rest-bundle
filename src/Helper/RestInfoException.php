<?php

namespace MNC\RestBundle\Helper;

/**
 * Class RestInfoException
 * @package MNC\RestBundle\Helper
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RestInfoException extends \Exception
{
    /**
     * @param $method
     * @param $property
     * @return RestInfoException
     */
    public static function nullProperty($method, $property)
    {
        return new self(sprintf('Error in %s method: Property %s is null.', $method, $property));
    }

    /**
     * @param $method
     * @param $class
     * @return RestInfoException
     */
    public static function classDoesNotExist($method, $class)
    {
        return new self(sprintf('Error in %s method: Class %s does not exist.', $method, $class));
    }

    /**
     * @param $childClass
     * @param $parentClass
     * @return RestInfoException
     */
    public static function inheritanceException($type, $childClass, $parentClass)
    {
        return new self(sprintf('Your %s class %s should extend %s'), $type, $childClass, $parentClass);
    }
}