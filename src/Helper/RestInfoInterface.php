<?php

namespace MNC\RestBundle\Helper;

/**
 * Interface RestInfoInterface
 * @package MNC\RestBundle\Helper
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface RestInfoInterface
{
    /**
     * Gets the resource name. Passing $plural = true will return the pluralized name.
     * @return string
     */
    public function getName($plural = false);

    /**
     * Returns the identifier for that entity. This identifier is used in the uri.
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns a verb defining the action according to the route. This may be
     * see, modify and delete. Uselful for error messages.
     * @return string
     */
    public function getActionVerb();

    /**
     * Returns the entity FQCN.
     * @return string
     */
    public function getEntityClass();

    /**
     * Returns the form FQCN. Passing $edit = true will return the Edit form if exists.
     * @return string
     */
    public function getFormClass($edit = false);

    /**
     * Returns the object manager FQCN.
     * @return string
     */
    public function getManagerClass();

    /**
     * Returns the entity transformer FQCN.
     * @return mixed
     */
    public function getTransformerClass();
}