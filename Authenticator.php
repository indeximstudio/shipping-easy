<?php

namespace Indeximstudio\ShippingEasy;

class Authenticator
{
    /**
     * Instantiates a new authenticator object.
     *
     * http_method - The method of the http request. E.g. "post" or "get".
     * path - The path of the request's uri. E.g. "/orders/callback"
     * params - An associative array of the request's query string parameters. E.g. array("api_signature" => "asdsadsad", "api_timestamp" => "1234567899")
     * json_body - The request body as a JSON string.
     * api_secret - The ShippingEasy API secret for the store. Defaults to the global configuration if set.
     */

    private $suppliedSignatureString;
    private $expectedSignature;

    /**
     * Authenticator constructor.
     * @param $httpMethod
     * @param $path
     * @param $params
     * @param $jsonBody
     * @param $apiSecret
     */
    public function __construct($httpMethod = null, $path = null, $params = null, $jsonBody = null, $apiSecret = null)
    {
        $apiSecret = isset($apiSecret) ? $apiSecret : ShippingEasy::$apiSecret;
        $this->suppliedSignatureString = $params['api_signature'];
        unset($params['api_signature']);
        $this->expectedSignature = new Signature($apiSecret, $httpMethod, $path, $params, $jsonBody);
    }

    public function getExpectedSignature()
    {
        return $this->expectedSignature;
    }

    public function getSuppliedSignatureString()
    {
        return $this->suppliedSignatureString;
    }

    public function isAuthenticated()
    {
        return $this->getExpectedSignature()->equals($this->getSuppliedSignatureString());
    }

}