<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $repository_full_class_name;?>;
use Doctrine\ORM\EntityManagerInterface;
use MNC\RestBundle\Manager\AbstractResourceManager;
use Symfony\Component\HttpFoundation\Request;

class <?= $class_name; ?> extends AbstractResourceManager
{
    /**
     * This method is only called if you are using the RestfulActions trait.
     *
     * This method is called in your index method to fetch resources from your
     * database. It can return an instance of QueryBuilder, ArrayCollection,
     * PersistentCollection or simply an array.
     * You can also return a response directly.
     * Works best when you return a QueryBuilder object ready to be fetched.
     *
     * Use this method to manage your listing of resources according to your
     * bussiness rules. For managing permissions to other single resources you can
     * use the OnwnableInterface or the RestrictableInterface.
     *
     * @param Request $request
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function indexResource(Request $request)
    {
        /** @var <?= $repository_class_name; ?> $repo */
        $repo = $this->getRepository($this->getRestInfo()->getEntityClass());
        $query = $repo->createQueryBuilder('<?= $entity_alias; ?>');
        return $query;
    }
}