<?php

namespace MNC\RestBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use MNC\RestBundle\Annotations\RestfulController;
use MNC\RestBundle\Helper\RouteActionVerb;
use MNC\RestBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This Listener checks wether a Controller is tagged with the @RestfulController
 * annotation. Also checks if this controller extends MNC\RestBundle\Controller\RestController
 * If so, sets the controller to work with the annotation options, leveraging all
 * the power of the annotation.
 *
 * @package MNC\RestBundle
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RestControllerListener
{
    const REST_ANNOTATION = 'MNC\RestBundle\Annotations\RestfulController';

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

        /** @var RestfulController $annot */
        $annot = $this->reader->getClassAnnotation($reflController, self::REST_ANNOTATION);

        if ($annot === null ) {
            return;
        }

        $restController = RestController::class;
        if (!$reflController->isSubclassOf($restController)) {
            throw new \Exception("To use @ResfulController annotation your $controller must extend $restController");
        }

        /** @var RestController $object */
        $object = $event->getController()[0];

        $pa = PropertyAccess::createPropertyAccessor();

        if ($pa->isWritable($object, 'name')) {
            $pa->setValue($object, 'name', $annot->resourceName);
        }
        if ($pa->isWritable($object, 'entity')) {
            $pa->setValue($object, 'entity', $annot->relatedEntity);
        }
        if ($pa->isWritable($object, 'identifier')) {
            $pa->setValue($object, 'identifier', $annot->identifier);
        }
        if ($pa->isWritable($object, 'form')) {
            $pa->setValue($object, 'form', $annot->formClass);
        }
        if ($pa->isWritable($object, 'transformer')) {
            $pa->setValue($object, 'transformer', $annot->transformerClass);
        }
        if ($pa->isWritable($object, 'action')) {
            $pa->setValue($object, 'action', $this->evaluateAction($request));
        }

        // We also put the RestController info in the request attributes so we can reuse them in other services
        $request->attributes->set('_resource_name', $annot->resourceName);
        $request->attributes->set('_related_entity', $annot->relatedEntity);
        $request->attributes->set('_identifier', $annot->identifier);
        $request->attributes->set('_form_class', $annot->formClass);
        $request->attributes->set('_transformer_class', $annot->transformerClass);
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