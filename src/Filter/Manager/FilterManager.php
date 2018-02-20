<?php

namespace MNC\RestBundle\Filter\Manager;

use MNC\RestBundle\Filter\FilterInterface;
use MNC\RestBundle\Filter\QBFilter;
use MNC\RestBundle\Filter\Resolver\FilterResolverInterface;

/**
 * Class FilterManager
 * @package MNC\RestBundle\Filter\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FilterManager implements FilterManagerInterface
{
    /**
     * @var FilterResolverInterface
     */
    private $filterResolver;
    /**
     * @var FilterInterface[]
     */
    private $filters;

    public function __construct(FilterResolverInterface $filterResolver)
    {
        $this->filterResolver = $filterResolver;
    }

    public function register(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function resolve($data)
    {
        return $this->filterResolver->applyFilters($data, $this->filters);
    }
}