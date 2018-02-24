<?php

namespace MNC\RestBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use MNC\RestBundle\Annotations\Resource;
use MNC\RestBundle\Annotations\UriIdentifier;
use MNC\RestBundle\Helper\RestInfo;
use MNC\RestBundle\Helper\RouteActionVerb;
use MNC\RestBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This Listener checks wether a Controller is tagged with the @Resource
 * annotation. Also checks if this controller extends MNC\RestBundle\Controller\RestController
 * If so, sets the controller to work with the annotation options, leveraging all
 * the power of the annotation.
 *
 * @package MNC\RestBundle
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RestControllerListener
{
    const RESOURCE_ANNOT = 'MNC\RestBundle\Annotations\Resource';
    const IDENTIFIER_ANNOT = 'MNC\RestBundle\Annotations\UriIdentifier';
    const MANAGER_ANNOT = 'MNC\RestBundle\Annotations\ResourceManager';

    /**
     * @var Reader
     */
    private $reader;

    /**
     * RestControllerListener constructor.
     * @param Reader                $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param FilterControllerEvent $event
     * @throws \Exception
     * @throws \TypeError
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $action = $request->attributes->get('_controller');
        $controller = explode('::', $action)[0];

        // We try to reflect the controller.
        // If there's an exception, means that this is not a valid controller,
        // but maybe the profiler or a twig exception.
        try {
            $reflController = new \ReflectionClass($controller);
        } catch (\Exception $e) {
            return;
        }

        /** @var Resource $annot */
        $restAnnot = $this->reader->getClassAnnotation($reflController, self::RESOURCE_ANNOT);

        if ($restAnnot === null ) {
            return;
        }

        $restController = RestController::class;
        if (!$reflController->isSubclassOf($restController)) {
            throw new \Exception(sprintf('To use %s annotation your %s must extend %s', self::RESOURCE_ANNOT, $controller, $restController));
        }

        // We reflect the entity to check its annotations.
        $reflEntity = new \ReflectionClass($restAnnot->relatedEntity);

        // We check for the identifier annotation.
        $identifier = 'id';
        /** @var UriIdentifier $idAnnot */
        $idAnnot = $this->reader->getClassAnnotation($reflEntity, self::IDENTIFIER_ANNOT);
        if ($idAnnot !== null) {
            $identifier = $idAnnot->name;
        }

        // We check for the object manager annotation.
        $manager = null;
        /** @var UriIdentifier $managerAnnot */
        $managerAnnot = $this->reader->getClassAnnotation($reflEntity, self::MANAGER_ANNOT);
        if ($managerAnnot !== null) {
            $manager = $managerAnnot->name;
        }

        /** @var RestController $object */
        $controllerInstance = $event->getController()[0];

        // We put the RestController info in the request attributes so we can reuse them in other services
        $request->attributes->set('_resource_name', $restAnnot->resourceName);
        $request->attributes->set('_related_entity', $restAnnot->relatedEntity);
        $request->attributes->set('_identifier', $identifier);
        $request->attributes->set('_form_class', $restAnnot->formClass);
        $request->attributes->set('_transformer_class', $restAnnot->transformerClass);
        $request->attributes->set('_manager_class', $manager);
        $request->attributes->set('_action_verb', $this->evaluateAction($request));

        $restInfo = RestInfo::createFromRequest($request);

        $controllerInstance->boot($restInfo);
    }

    /**
     * Evaluates the route to get an action verb.
     * @param Request $request
     * @return string
     */
    private function evaluateAction(Request $request)
    {
        $route = $request->attributes->get('_route');
        return RouteActionVerb::findVerb($route);
    }
}