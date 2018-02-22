<?php

namespace MNC\RestBundle\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
use MNC\RestBundle\Helper\RestInfoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface ResourceManagerInterface
 * @package MNC\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ResourceManagerInterface extends ObjectManager
{
    /**
     * @return RestInfoInterface
     */
    public function getRestInfo();

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager();

    /**
     * This method is only called if you are using the RestfulActions trait.
     *
     * This method is called in your index method to fetch resources from your
     * database. It can return an instance of QueryBuilder, ArrayCollection,
     * PersistentCollection or simply an array.
     * You can also return a response directly.
     * Works best when you return a QueryBuilder object ready to be fetched.
     *
     * Use this method to manage your listing of resources according to your
     * bussiness rules. For managing permissions to other single resources you can
     * use the OnwnableInterface or the RestrictableInterface.
     *
     * @param Request $request
     * @return QueryBuilder|ArrayCollection|PersistentCollection|array|Response
     */
    public function indexResource(Request $request);
}