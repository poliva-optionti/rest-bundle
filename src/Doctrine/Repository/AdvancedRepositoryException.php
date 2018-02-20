<?php

namespace MNC\RestBundle\Doctrine\Repository;

/**
 * Class AdvancedRepositoryException
 * @package MNC\RestBundle\Doctrine\Repository
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class AdvancedRepositoryException extends \Exception
{
    public static function invalidAssociationName($class, $association)
    {
        return new self(sprintf('The class %s does not have a association named %s. Can not perform with() call.', $class, $association));
    }

    public static function invalidAllCall($class)
    {
        return new self(sprintf('Cannot use the all() method on a dirty QueryBuilder instance, in class %s.', $class));
    }

    public static function invalidScopeCall($class, $scopeMethod)
    {
        return new self(sprintf('Scope method %s() does not exist in repository %s.', $class, $scopeMethod));
    }
}