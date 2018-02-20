<?php

namespace MNC\RestBundle\ApiProblem;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    /**
     * @var ApiProblem
     */
    private $apiProblem;

    /**
     * ApiProblemException constructor.
     * @param ApiProblem      $apiProblem
     * @param \Exception|null $previous
     * @param array           $headers
     * @param int             $code
     */
    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->apiProblem = $apiProblem;
        $statusCode = $apiProblem->getStatus();
        $message = $apiProblem->getTitle();
        $headers = ['Content-Type' => ApiProblem::CONTENT_TYPE];
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * FactoryMethod for ApiProblemException.
     * @param ApiProblem $apiProblem
     * @return ApiProblemException
     */
    public static function createFromApiProblem(ApiProblem $apiProblem)
    {
        return new self($apiProblem);
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem()
    {
        return $this->apiProblem;
    }
}