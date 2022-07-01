<?php

namespace App\Exceptions;

use Exception;

class MessageServiceException extends Exception
{
    private int $httpCode;

    public function __construct(string $message, int $httpCode, int $code = 0, ?Throwable $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
