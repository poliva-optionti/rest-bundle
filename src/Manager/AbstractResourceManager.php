<?php

namespace MNC\RestBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use MNC\RestBundle\Helper\RestInfo;
use MNC\RestBundle\Helper\RestInfoInterface;

/**
 * This class is a Wrapper of the original EntityManager. It makes some of it's
 * methods a little bit less verbose.
 * @package MNC\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
abstract class AbstractResourceManager implements ResourceManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var RestInfoInterface
     */
    private $restInfo;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param RestInfoInterface $restInfo
     */
    public function setRestInfo(RestInfoInterface $restInfo)
    {
        $this->restInfo = $restInfo;
    }

    /**
     * @return RestInfoInterface
     */
    public function getRestInfo()
    {
        return $this->restInfo;
    }

    public function find($className, $id)
    {
        return $this->em->find($className, $id);
    }

    public function persist($object)
    {
        return $this->em->persist($object);
    }

    public function remove($object)
    {
        return $this->em->remove($object);
    }

    public function merge($object)
    {
        return $this->em->merge($object);
    }

    public function clear($objectName = null)
    {
        return $this->em->clear($objectName = null);
    }

    public function detach($object)
    {
        return $this->em->detach($object);
    }

    public function refresh($object)
    {
        return $this->em->refresh($object);
    }

    public function flush()
    {
        return $this->em->flush();
    }

    public function getRepository($className)
    {
        return $this->em->getRepository($className);
    }

    public function getClassMetadata($className)
    {
        return $this->em->getClassMetadata($className);
    }

    public function getMetadataFactory()
    {
       return $this->em->getMetadataFactory();
    }

    public function initializeObject($obj)
    {
        return $this->initializeObject($obj);
    }

    public function contains($object)
    {
        return $this->em->contains($object);
    }
}