<?php

namespace Indeximstudio\ShippingEasy;

class InvalidRequestError extends Error
{
    public function __construct($message, $http_status = null, $http_body = null, $json_body = null)
    {
        parent::__construct($message, $http_status, $http_body, $json_body);
    }
}
