<?php

namespace Indeximstudio\ShippingEasy\Exceptions;

use Throwable;

class InvalidRequestException extends Exception
{
    public function __construct($message = 'Invalid request !', $code = 404, $body = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $body, $previous);
    }
}
