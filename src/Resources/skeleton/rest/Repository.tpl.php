<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

use <?= $entity_full_class_name; ?>;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method <?= $entity_class_name; ?>|null find($id, $lockMode = null, $lockVersion = null)
 * @method <?= $entity_class_name; ?>|null findOneBy(array $criteria, array $orderBy = null)
 * @method <?= $entity_class_name; ?>[]    findAll()
 * @method <?= $entity_class_name; ?>[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class <?= $class_name; ?> extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, <?= $entity_class_name; ?>::class);
    }

    /**
     * Finds a <?= $entity_class_name; ?> object by its configured identifier.
     * Supports multiple comma separated ids.
     * This method works with the RestController. Do not touch.
     * @param $identifier
     * @param $value
     * @return QueryBuilder
     */
    public function findByIndentifierQuery($identifier, $value)
    {
        $query = $this->createQueryBuilder('<?= $entity_alias; ?>');
        if (strpos(',', $value) === false) {
            $value = explode(',', $value);
            return $query->where($query->expr()->in('<?= $entity_alias; ?>.'.$identifier, ':value'))
                ->setParameter('value', $value);
        }
        return $query->where(sprintf('<?= $entity_alias; ?>.%s = :value', $identifier))
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}
