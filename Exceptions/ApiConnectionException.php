<?php

namespace Indeximstudio\ShippingEasy\Exceptions;

use Throwable;

class ApiConnectionException extends Exception
{
    public function __construct($message = 'Api connection exception !', $code = 505, $body = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $body, $previous);
    }
}
