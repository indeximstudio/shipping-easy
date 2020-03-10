<?php

namespace Indeximstudio\ShippingEasy;

class Object
{
    public function request($meth, $path, $params = null, $payload = null, $apiKey = null, $apiSecret = null)
    {
        $requester = new ApiRequester();

        return $requester->request($meth, $path, $params, $payload, $apiKey, $apiSecret);
    }

}

