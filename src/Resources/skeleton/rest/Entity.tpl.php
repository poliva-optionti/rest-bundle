<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Doctrine\ORM\Mapping as ORM;
use MNC\RestBundle\Annotations\ResourceManager;
use MNC\RestBundle\Annotations\UriIdentifier;

/**
 * @ORM\Entity(repositoryClass="<?= $repository_full_class_name ?>")
 * @UriIdentifier("id")
 * @ResourceManager("<?= $manager_full_class_name ?>")
 */
class <?= $class_name."\n" ?>
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    // add your own fields
}
