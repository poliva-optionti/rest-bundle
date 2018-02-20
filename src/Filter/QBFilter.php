<?php

namespace MNC\RestBundle\Filter;
use Doctrine\ORM\QueryBuilder;

/**
 * Class QBFilter
 * @package MNC\RestBundle\Filter
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class QBFilter extends Filter
{
    public static function equalsTo($field, $value)
    {
        return new self('equals', function (QueryBuilder $query, $alias) use ($field, $value) {
            return $query->andWhere($query->expr()->eq(sprintf('%s.%s', $alias, $field), ':value'))
                ->setParameter('value', $value);
        });
    }

    public static function greaterTan($field, $value)
    {
        return new self('greater', function (QueryBuilder $query, $alias) use ($field, $value) {
            return $query->andWhere($query->expr()->gt(sprintf('%s.%s', $alias, $field), ':value'))
                ->setParameter('value', $value);
        });
    }

    public static function lesserThan($field, $value)
    {
        return new self('lesser', function (QueryBuilder $query, $alias) use ($field, $value) {
            return $query->andWhere($query->expr()->lt(sprintf('%s.%s', $alias, $field), ':value'))
                ->setParameter('value', $value);
        });
    }

    public static function in($field, array $data)
    {
        return new self('in', function (QueryBuilder $query, $alias) use ($field, $data) {
            return $query->andWhere($query->expr()->in(sprintf('%s.%s', $alias, $field), ':data'))
                ->setParameter('data', $data);
        });
    }
}