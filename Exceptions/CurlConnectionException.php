<?php

namespace Indeximstudio\ShippingEasy\Exceptions;

use Throwable;

class CurlConnectionException extends Exception
{
    public function __construct($message = 'Curl connection exception !', $code = 506, $body = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $body, $previous);
    }
}
