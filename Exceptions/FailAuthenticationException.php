<?php

namespace Indeximstudio\ShippingEasy\Exceptions;

use Throwable;

class FailAuthenticationException extends Exception
{
    public function __construct($message = 'Authentication failed!', $code = 401, $body = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $body, $previous);
    }
}
