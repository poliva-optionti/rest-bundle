<?php

namespace MNC\RestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Trait NoShowActionTrait
 * @package MNC\RestBundle\Controller
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait NoShowActionTrait
{
    /**
     * @param Request $request
     */
    public function showAction(Request $request, $id)
    {
        $route = $request->getPathInfo();
        throw new RouteNotFoundException("Route $route not found");
    }
}