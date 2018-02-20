<?php

namespace MNC\RestBundle\Fractalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class Fractalizer
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Manager
     */
    private $manager;

    /**
     * Fractalizer constructor.
     * @param RequestStack    $requestStack
     * @param RouterInterface $router
     * @param Manager         $manager
     */
    public function __construct(RequestStack $requestStack, RouterInterface $router, Manager $manager)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->manager = $manager;
    }

    /**
     * @param                     $data
     * @param TransformerAbstract $transformer
     * @return array
     * @throws \Exception
     */
    public function fractalize($data, TransformerAbstract $transformer)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($data instanceof QueryBuilder && $request->query->has('with')) {
            $data = $this->eagerLoadWith($data);
        }

        if ($this->isPluralResponse($data)) {

            // $this->resolveCollectionFilters($data);

            $paginator = $this->instantiatePaginator($data);
            $results = $paginator->getPaginator()->getCurrentPageResults();

            $resource = new Collection($results, $transformer);
            $resource->setPaginator($paginator);
            if ($request->query->has('with')) {
                $this->manager->parseIncludes($request->query->get('with'));
            }
        } else {
            if ($request->query->has('with')) {
                $this->manager->parseIncludes($request->query->get('with'));
            }
            $resource = new Item($data, $transformer);
        }
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * Returns an instance of the paginator
     * @param $data
     * @return PagerfantaPaginatorAdapter
     * @throws \Exception
     */
    private function instantiatePaginator($data)
    {
        $request = $this->requestStack->getCurrentRequest();
        $router = $this->router;

        $adapter = $this->instantiateAdapter($data);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage((int) $request->query->get('size') ?: 10);
        $pagerfanta->setCurrentPage((int) $request->query->get('page') ?: 1);

        $paginator = new PagerfantaPaginatorAdapter($pagerfanta, function($page) use ($request, $router) {
            $route = $request->attributes->get('_route');
            $params = $request->attributes->get('_route_params');
            $newParams = array_merge($params, $request->query->all());
            $newParams['page'] = $page;
            return $router->generate($route, $newParams, 0);
        });

        return $paginator;
    }

    /**
     * This method tries to guess if data is a collection of items or just a single
     * resource.
     * @param $data
     * @return bool
     */
    private function isPluralResponse($data)
    {
        if ($data instanceof QueryBuilder OR $data instanceof ArrayCollection OR $data instanceof PersistentCollection) {
            return true;
        } elseif (is_array($data) AND sizeof($data) > 0 AND is_object($data[0])) {
            return true;
        } elseif (is_object($data)) {
            return false;
        }
        return false;
    }

    /**
     * @param $data
     * @return ArrayAdapter|DoctrineCollectionAdapter|DoctrineORMAdapter
     * @throws \Exception
     */
    private function instantiateAdapter($data)
    {
        if ($data instanceof QueryBuilder) {
            return new DoctrineORMAdapter($data, false);
        }

        if ($data instanceof ArrayCollection OR $data instanceof PersistentCollection) {
            return new DoctrineCollectionAdapter($data);
        }

        if (is_array($data)) {
            return new ArrayAdapter($data);
        }

        throw FractalizerException::adapterNotFound();
    }

    /**
     * Checks the with query param and eager loads the relationships.
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    private function eagerLoadWith(QueryBuilder $query)
    {
        $request = $this->requestStack->getCurrentRequest();
        $withs = explode(',', $request->query->get('with'));
        $assoc = $query->getEntityManager()->getClassMetadata($request->attributes->get('_related_entity'))->getAssociationNames();
        $root = $request->attributes->get('_resource_name');

        foreach ($withs as $field) {
            if (strpos('.', $field) !== false) {
                $subrelations = explode('.', $field);
                for ($i = 0; $i === sizeof($subrelations) - 1; $i++) {
                    if ($i === 0) {
                        $query->leftJoin($root.'.'.$subrelations[$i], $subrelations[$i]);
                    } else {
                        $query->leftJoin($subrelations[$i -1].'.'.$subrelations[$i], $subrelations[$i]);
                    }
                    $query->addSelect($subrelations[$i]);
                }
            } else {
                if (in_array($field, $assoc)) {
                    $query->leftJoin($root.'.'.$field, $field)
                        ->addSelect($field);
                }
            }
        }
        return $query;
    }

    /**
     * Resolves the collection filters.
     * @param mixed $data
     */
    private function resolveCollectionFilters($data)
    {
        return $this->filterManager->resolve($data);
    }
}