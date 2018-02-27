<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\Routing\Annotation\Route;
use MNC\RestBundle\Annotations\Resource;
use MNC\RestBundle\Controller\RestController;
use MNC\RestBundle\Controller\RestfulActionsTrait;

/**
 * @Route("/<?= $resource_name_plural; ?>")
 * @Resource("<?= $resource_name; ?>",
 *     relatedEntity="<?= $entity_full_class_name; ?>",
 *     formClass="<?= $form_full_class_name;?>",
 *     transformerClass="<?= $transformer_full_class_name; ?>")
 */
class <?= $class_name; ?> extends RestController
{
    use RestfulActionsTrait;

    // You can override endpoints or create your custom ones here.
}