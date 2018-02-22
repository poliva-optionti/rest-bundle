<?php

namespace MNC\RestBundle\EventListener;

use Doctrine\DBAL\Exception\DriverException;
use MNC\RestBundle\ApiProblem\ApiError;
use MNC\RestBundle\ApiProblem\ApiProblem;
use MNC\RestBundle\ApiProblem\ApiProblemException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * This class is in charge of listening for an exception thrown inside the api
 * firewall and formatting it in a consistent way. The format for errors used is
 * the one defined in RFC7807: Problem Details for HTTP APIs.
 *
 * @package MNC\RestBundle\EventListener
 * @author Matías Navarro Carter <mnavarro@option.cl>
 * @docs https://tools.ietf.org/html/rfc7807
 */
class ApiExceptionListener
{
    /**
     * @var string
     */
    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->environment === 'dev') {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof ApiProblemException) {
            $response =  $exception->getApiProblem()->toJsonResponse();
            $event->setResponse($response);
            return;
        }

        if ($exception instanceof HttpException) {
            $apiProblem = ApiProblem::create($exception->getStatusCode(), $exception->getMessage());
            $response = $apiProblem->toJsonResponse();
            $event->setResponse($response);
            return;
        }

        if ($exception instanceof DriverException) {
            if ($this->environment === 'dev') {
                $detail = $exception->getMessage();
            } else {
                $detail = 'SQLSTATE '.$exception->getSQLState();
            }
            $apiProblem = ApiProblem::create(500, $detail, ApiError::TYPE_DATABASE_ERROR);
            $response = $apiProblem->toJsonResponse();
            $event->setResponse($response);
            return;
        }

        if ($exception instanceof \Exception) {
            $apiProblem = ApiProblem::create(500, $exception->getMessage());
            $apiProblem->setInstance(get_class($exception));
            $response = $apiProblem->toJsonResponse();
            $event->setResponse($response);
            return;
        }

        return;
    }
}