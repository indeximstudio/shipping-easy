<?php

namespace Indeximstudio\ShippingEasy;

class SignedUrl
{
    private $params;
    private $path;

    public function __construct($httpMethod = null, $path = null, $params = null, $jsonBody = null, $apiTimestamp = null, $apiKey = null, $apiSecret = null)
    {
        $apiSecret = isset($apiSecret) ? $apiSecret : ShippingEasy::$apiSecret;
        $params['api_key'] = isset($apiKey) ? $apiKey : ShippingEasy::$apiKey;
        $params['api_timestamp'] = isset($apiTimestamp) ? $apiTimestamp : time();
        $signature_object = new Signature($apiSecret, $httpMethod, $path, $params, $jsonBody);
        $params['api_signature'] = $signature_object->encrypted();
        $this->params = $params;
        $this->path = $path;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function toString()
    {
        return ShippingEasy::$apiBase . $this->getPath() . '?' . http_build_query($this->getParams());
    }

}
