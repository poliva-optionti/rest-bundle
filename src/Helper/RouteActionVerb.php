<?php

namespace MNC\RestBundle\Helper;


class RouteActionVerb
{
    /**
     * Finds a verb depending of the route action name.
     * @param $route
     * @return string
     */
    static public function findVerb($route)
    {
        switch ($route) {
            case strpos($route, 'show') !== false:
                return 'see';
                break;
            case strpos($route, 'edit') !== false:
                return 'modify';
                break;
            case strpos($route, 'update') !== false:
                return 'modify';
                break;
            case strpos($route, 'delete') !== false:
                return 'delete';
                break;
            default:
                return 'perform this action on';
                break;
        }
    }
}