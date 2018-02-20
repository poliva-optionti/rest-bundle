<?php

namespace MNC\RestBundle\Fractalizer;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class FractalizerException
 * @package MNC\RestBundle\Fractalizer
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FractalizerException extends Exception
{
    /**
     * This exception is thrown when, based on the data sent, it cannot guess the
     * type of adapter to use for Pagerfanta.
     * @return FractalizerException
     */
    public static function adapterNotFound()
    {
        return new self('Cannot resolve adapter for Pagerfanta with the dataset included.');
    }
}