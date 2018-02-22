<?php

namespace MNC\RestBundle\Helper;

use Doctrine\Common\Inflector\Inflector;
use League\Fractal\TransformerAbstract;
use MNC\RestBundle\Manager\AbstractResourceManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class provides a reliable interface for storing RestInfo.
 * @package MNC\RestBundle\Helper
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RestInfo implements RestInfoInterface
{
    private $resource;

    private $identifier;

    private $action;

    private $entity;

    private $manager;

    private $form;

    private $transformer;

    public function __construct($resource, $identifier, $action, $entity, $form, $transformer, $manager = null)
    {
        $this->resource = $resource;
        $this->identifier = $identifier;
        $this->action = $action;
        $this->entity = $entity;
        $this->manager = $manager;
        $this->form = $form;
        $this->transformer = $transformer;
    }

    /**
     * @param Request $request
     * @return RestInfo
     */
    public static function createFromRequest(Request $request)
    {
        return new self(
            $request->attributes->get('_resource_name'),
            $request->attributes->get('_identifier'),
            $request->attributes->get('_action_verb'),
            $request->attributes->get('_related_entity'),
            $request->attributes->get('_form_class'),
            $request->attributes->get('_transformer_class'),
            $request->attributes->get('_manager_class')
        );
    }

    public function getName($plural = false)
    {
        if ($plural === true) {
            return Inflector::pluralize($this->resource);
        }
        return $this->resource;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getActionVerb()
    {
        return $this->action;
    }

    public function getEntityClass()
    {
        if (!class_exists($this->entity)) {
            throw RestInfoException::classDoesNotExist('getEntityClass()', $this->entity);
        }
        return $this->entity;
    }

    public function getFormClass($edit = false)
    {
        if (!class_exists($this->form)) {
            throw RestInfoException::classDoesNotExist('getFormClass()', $this->form);
        }
        return $this->form;
    }

    public function getManagerClass()
    {
        if ($this->manager === null) {
            return null;
        }
        if (!class_exists($this->manager)) {
            throw RestInfoException::classDoesNotExist('getManagerClass()', $this->manager);
        }
        if (!is_subclass_of($this->manager, AbstractResourceManager::class)) {
            throw RestInfoException::inheritanceException('manager', $this->manager, AbstractResourceManager::class);
        }
        return $this->manager;
    }

    public function getTransformerClass()
    {
        if (!class_exists($this->transformer)) {
            throw RestInfoException::classDoesNotExist('getTransformerClass()', $this->transformer);
        }
        if (!is_subclass_of($this->transformer, TransformerAbstract::class)) {
            throw RestInfoException::inheritanceException('transformer', $this->manager, AbstractObjectManager::class);
        }
        return $this->transformer;
    }
}