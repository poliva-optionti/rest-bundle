<?php

namespace MNC\RestBundle\Doctrine\Fixtures;

/**
 * Interface FixtureCollectionInterface
 * @package MNC\RestBundle\Doctrine\Fixtures
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface FixtureCollectionInterface
{
    /**
     * Gets a reference for an element in the collection. If key is not specified
     * is a random element.
     * @param $key
     * @return mixed
     */
    public function get($key = null);

    /**
     * Fetches an element from the collection and moves it to fetched elements.
     * @param $key
     * @return mixed
     */
    public function fetch($key = null);

    /**
     * @param int|array $number
     * @return mixed
     */
    public function getSome($number = 3);

    /**
     * @param int|array $number
     * @return mixed
     */
    public function fetchSome($number = 3);

    /**
     * Checks if a collection is dirty.
     * @return bool
     */
    public function isDirty();

    /**
     * Restores the collection to it's original state
     * @return bool
     */
    public function resetState();
}