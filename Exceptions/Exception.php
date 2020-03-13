<?php

namespace Indeximstudio\ShippingEasy\Exceptions;

use Throwable;

class Exception extends \Exception
{
    private $body;

    public function __construct($message = 'Shipping Easy Exception !', $code = 500, $body = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }
}