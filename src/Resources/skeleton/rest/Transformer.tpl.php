<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use League\Fractal\TransformerAbstract;
use <?= $entity_full_class_name ?>;

class <?= $class_name ?> extends TransformerAbstract
{
    protected $availableIncludes = [];

    protected $defaultIncludes = [];

    protected $validParams = [];

    public function transform(<?= $entity_class_name ?> $<?= $resource_name ?>)
    {
        return [
            'id' => $<?= $resource_name ?>->getId(),
        ];
    }

    // Custom includes here

}
