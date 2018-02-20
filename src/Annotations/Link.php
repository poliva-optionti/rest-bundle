<?php

namespace MNC\RestBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * This class holds data for the link annotation.
 * @package MNC\RestBundle\Annotations
 * @author Matías Navarro Carter <mnavarro@option.cl>
 * @Annotation
 * @Target("CLASS")
 */
final class Link
{
    /**
     * @var string
     * @Required()
     */
    public $rel;

    /**
     * @var string
     * @Required()
     */
    public $route;

    /**
     * @var string
     * @Required()
     */
    public $method;

    /**
     * @var array
     */
    public $onAction = [];

    /**
     * @var array
     */
    public $params = [];
}