<?php

namespace MNC\RestBundle\Filter\Manager;

use MNC\RestBundle\Filter\FilterInterface;

/**
 * Interface FilterManagerInterface
 * @package MNC\RestBundle\Filter\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface FilterManagerInterface
{
    /**
     * @param FilterInterface $filter
     * @return mixed
     */
    public function register(FilterInterface $filter);

    /**
     * @param $data
     * @return mixed
     */
    public function resolve($data);
}