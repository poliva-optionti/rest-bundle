<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use MNC\RestBundle\Doctrine\Fixtures\AdvancedFixture;
use Doctrine\Common\Persistence\ObjectManager;
use <?= $entity_full_class_name; ?>;
use Faker\Generator;

class <?= $class_name; ?> extends AdvancedFixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->fake($manager, <?= $entity_class_name; ?>::class, 50, function(<?= $entity_class_name; ?> $<?= $resource_name; ?>, Generator $faker) {
            // $<?= $resource_name; ?>->setSomeField($faker->name);
            return $<?= $resource_name; ?>;
        });
    }
}