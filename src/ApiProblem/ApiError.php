<?php

namespace MNC\RestBundle\ApiProblem;

/**
 * Class ApiError
 * @package MNC\RestBundle\ApiProblem
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class ApiError
{
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'error:body_format';
    const TYPE_VALIDATION_ERROR = 'error:validation';
    const TYPE_AUTHORIZATION_ERROR = 'error:authorization';
    const TYPE_DATABASE_ERROR = 'error:database';
}