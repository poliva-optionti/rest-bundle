<?php

namespace MNC\RestBundle\Doctrine\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface AdvancedEntityRepositoryInterface
 * @package MNC\RestBundle\Doctrine\Repository
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface AdvancedEntityRepositoryInterface
{
    /** Sets all the records.
     * @return QueryBuilder
     */
    public function all();

    /**
     * Executes the query and fetches the result
     * @return mixed
     */
    public function get();

    /**
     * Sets a join in the query builder
     * @param $association
     * @return QueryBuilder
     */
    public function with($association);

    /**
     * Returns the QueryBuilder
     * @return QueryBuilder
     */
    public function getQueryBuilder();
}