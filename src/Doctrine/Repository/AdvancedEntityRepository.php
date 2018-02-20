<?php

namespace MNC\RestBundle\Doctrine\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;

/**
 * An Eloquent-Like API for Symfony's query builder.
 * @package MNC\RestBundle\Doctrine\Repository
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
abstract class AdvancedEntityRepository extends EntityRepository implements AdvancedEntityRepositoryInterface
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @var string
     */
    private $alias;

    /**
     * AdvancedEntityRepository constructor.
     * @param EntityManagerInterface $em
     * @param Mapping\ClassMetadata  $class
     */
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->alias = $class->table['name'];
        $this->qb = $this->createQueryBuilder($this->alias);
    }

    /**
     * @return QueryBuilder|mixed
     * @throws AdvancedRepositoryException
     */
    public function all()
    {
        if ($this->qb->getState() === QueryBuilder::STATE_DIRTY) {
            throw AdvancedRepositoryException::invalidAllCall($this->_class->getName());
        }
        return $this->get();
    }

    /**
     * Gets the result and restarts the QueryBuilder
     * @return mixed
     */
    public function get()
    {
        $query = $this->qb->getQuery();
        $this->qb = $this->createQueryBuilder($this->alias);
        return $query->getResult();
    }

    /**
     * @param        $association
     * @param string $joinType inner|left|outer
     * @return QueryBuilder
     * @throws AdvancedRepositoryException
     */
    public function with($association, $joinType = 'inner')
    {
        if (!$this->_class->hasAssociation($association)) {
            throw AdvancedRepositoryException::invalidAssociationName($this->_class->getName(), $association);
        }
        return $this->qb
            ->{$joinType . 'Join'}($this->alias . '.' . $association, $association)
            ->addselect($association);
    }

    /**
     * Performs operations in the db by chunking resultsets and applying a
     * a closure to them.
     * @param          $size
     * @param callable $callback
     * @return bool
     */
    public function chunk($size, callable $callback)
    {
        $offset = 0;
        do {
            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $this->qb->setMaxResults($size)->setFirstResult($offset);
            $results = new ArrayCollection($this->get());
            $countResults = $results->count();
            if ($countResults == 0) {
                break;
            }
            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $offset) === false) {
                return false;
            }
            unset($results);
            $offset = $offset + $size;
        } while ($countResults == $size);
        return true;
    }

    /**
     * @param $clause
     * @return QueryBuilder
     */
    public function where($clause)
    {
        return $this->qb->andWhere($clause);
    }

    /**
     * @param int $size
     * @param int $page
     * @return QueryBuilder
     */
    public function paginate($size = 10, $page = 1)
    {
        return $this->qb->setMaxResults($size)->setFirstResult($size * $page);
    }

    /**
     * @param $field
     * @param $type
     * @return QueryBuilder
     */
    public function orderBy($field, $type)
    {
        return $this->qb->orderBy($this->alias . '.' . $field, $type);
    }

    /**
     * @param bool $refresh
     * @return QueryBuilder
     */
    public function getQueryBuilder($refresh = false)
    {
        if ($refresh) {
            $qb = $this->qb;
            $this->qb = $this->createQueryBuilder($this->alias);
            return $qb;
        }
        return $this->qb;
    }

    /**
     * @param string $method
     * @param array  $arguments
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws AdvancedRepositoryException
     */
    public function __call($method, $arguments)
    {
        if (strpos($method, 'scope') === false) {
            $scope = 'scope' . ucfirst($method);
            if (method_exists($this, $scope)) {
                return $this->$scope(array_merge([$this->qb], $arguments));
            }
            throw AdvancedRepositoryException::invalidScopeCall(get_class($this), $scope);
        }
        return parent::__call($method, $arguments);
    }

    public function getAlias()
    {
        return $this->alias;
    }
}