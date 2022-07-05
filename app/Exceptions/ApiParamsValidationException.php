<?php

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ApiParamsValidationException extends RuntimeException
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
