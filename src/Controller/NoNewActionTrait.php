<?php

namespace MNC\RestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Trait NoNewActionTrait
 * @package MNC\RestBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait NoNewActionTrait
{
    /**
     * @param Request $request
     */
    public function newAction(Request $request)
    {
        $route = $request->getPathInfo();
        throw new RouteNotFoundException("Route $route not found");
    }

    /**
     * @param Request $request
     */
    public function storeAction(Request $request)
    {
        $route = $request->getPathInfo();
        throw new RouteNotFoundException("Route $route not found");
    }
}