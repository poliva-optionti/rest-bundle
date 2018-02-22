<?php

namespace MNC\RestBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ResourceManager
{
    /**
     * @var string
     * @Required()
     */
    public $name;
}