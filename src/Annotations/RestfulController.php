<?php

namespace MNC\RestBundle\Annotations;


use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class RestfulController
{
    /**
     * @var string
     * @Required()
     */
    public $resourceName;

    /**
     * @var string
     * @Required()
     */
    public $relatedEntity;

    /**
     * @var string
     * @Required()
     */
    public $formClass;

    /**
     * @var string
     * @Required()
     */
    public $transformerClass;
}