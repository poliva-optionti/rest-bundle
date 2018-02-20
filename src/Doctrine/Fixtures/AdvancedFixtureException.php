<?php

namespace MNC\RestBundle\Doctrine\Fixtures;

/**
 * Class AdvancedFixtureException
 * @package CoreBundle\DataFixtures
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class AdvancedFixtureException extends \Exception
{
    public static function noCollectionForClass($class)
    {
        return new self(sprintf('Class %s does not have a collection defined and/or the collection is empty.', $class));
    }

    public static function invalidReturnValue($class)
    {
        return new self(sprintf('The callable for faking class %s must return an object instance of that class.', $class));
    }
}