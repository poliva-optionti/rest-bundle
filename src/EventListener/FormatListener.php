<?php

namespace MNC\RestBundle\EventListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class FormatListener
 * @package MNC\RestBundle\EventListener
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FormatListener
{
    private static $mimeTypes = [
        'image/jpeg' => 'jpeg',
        'image/png' => 'png',
        'image/jpg' => 'jpg'
    ];

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->headers->has('Content-Type')) {
            return;
        }

        $contentType = $request->headers->get('Content-Type');
        if (!array_key_exists($contentType, self::$mimeTypes)) {
            return;
        }

        $format = self::$mimeTypes[$contentType];
        $request->attributes->set('_format', $format);
    }
}