<?php

namespace MNC\RestBundle\Doctrine\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * This provides some functionality to create fixtures more efficiently, faking
 * some data and improving entity management and collection reference.
 * @package RestBundle
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
abstract class AdvancedFixture extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var FixtureCollection[]
     */
    protected $collectionRepository = [];

    /**
     * AdvancedFixture constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager          $manager
     * @param                        $class
     * @param                        $number
     * @param callable               $callable
     * @return FixtureCollection
     */
    public function fake(ObjectManager $manager, $class, $number, callable $callable)
    {
        // Fix collection! Is not taking records.
        $items = [];
        for ($i = 0; $i < $number; $i++) {
            $entity = new $class();
            $entity = $callable($entity, $this->faker);
            if (!$entity instanceof $class) {
                AdvancedFixtureException::invalidReturnValue($class);
            }
            $manager->persist($entity);
            $items[] = $entity;
        }
        $manager->flush();
        if (sizeof($items) === 1) {
            return array_shift($items);
        }
        return $this->getCollection($manager, $class);
    }

    /**
     * @param ObjectManager          $manager
     * @param                        $class
     * @return FixtureCollection
     */
    public function getCollection(ObjectManager $manager, $class)
    {
        $repo = $manager->getRepository($class);
        $items = $repo->findAll();
        return new FixtureCollection($items);
    }
}