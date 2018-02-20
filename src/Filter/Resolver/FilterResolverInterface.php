<?php

namespace MNC\RestBundle\Filter\Resolver;

/**
 * Interface FilterResolverInterface
 * @package MNC\RestBundle\Filter\Resolver
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface FilterResolverInterface
{
    public function applyFilters($data, $filters);
}