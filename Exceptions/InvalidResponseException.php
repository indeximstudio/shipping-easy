<?php

namespace Indeximstudio\ShippingEasy\Exceptions;

use Throwable;

class InvalidResponseException extends Exception
{
    public function __construct($message = 'Invalid response body !', $code = 502, $body = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $body, $previous);
    }
}
