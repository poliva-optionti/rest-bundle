<?php

namespace MNC\RestBundle\EventListener;
use MNC\RestBundle\ApiProblem\ApiError;
use MNC\RestBundle\ApiProblem\ApiProblem;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestBodyListener
 * @package MNC\RestBundle\EventListener
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RequestBodyListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $content = $request->getContent();
        if (!$content OR !$request->headers->get('content-type') === 'application/json') {
            return;
        }

        $data = json_decode($content, true);

        if (!$data) {
            $error = json_last_error_msg();
            $apiProblem = ApiProblem::create(422, $error, ApiError::TYPE_INVALID_REQUEST_BODY_FORMAT);
            $apiProblem->throwException();
        }

        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }
    }
}