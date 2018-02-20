<?php

namespace MNC\RestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Trait NoIndexActionTrait
 * @package MNC\RestBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait NoIndexActionTrait
{
    /**
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $route = $request->getPathInfo();
        throw new RouteNotFoundException("Route $route not found");
    }
}