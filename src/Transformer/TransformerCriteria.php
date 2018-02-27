<?php

namespace MNC\RestBundle\Transformer;
use Doctrine\Common\Collections\Criteria;
use League\Fractal\ParamBag;

/**
 * Class TransformerCriteria
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class TransformerCriteria
{
    public static function build(ParamBag $paramBag)
    {
        $criteria = Criteria::create();
        if ($paramBag->offsetExists('limit')) {
            list($limit, $offset) = $paramBag->get('limit');
            $criteria->setMaxResults($limit);
            $criteria->setFirstResult($offset);
        }
        if ($paramBag->offsetExists('order')) {
            list($orderCol, $orderBy) = $paramBag->get('order');
            $criteria->orderBy([$orderCol => $orderBy]);
        }
        return $criteria;
    }
}