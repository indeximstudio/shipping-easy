<?php

namespace Indeximstudio\ShippingEasy;

class Signature
{
    private $apiSecret;
    private $httpMethod;
    private $path;
    private $params;
    private $jsonBody;

    public function __construct($apiSecret = null, $httpMethod = null, $path = null, $params = null, $jsonBody = null)
    {
        $this->apiSecret = $apiSecret;
        $this->httpMethod = strtoupper($httpMethod);
        $this->path = $path;
        ksort($params);
        $this->params = $params;

        if (is_string($jsonBody)) {
            $this->jsonBody = str_replace("\/", '/', $jsonBody);
        } else {
            $this->jsonBody = json_encode($jsonBody);
        }
    }

    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getJsonBody()
    {
        return $this->jsonBody;
    }

    public function plaintext()
    {
        $parts = array($this->getHttpMethod());
        $parts[] = $this->getPath();

        if (!empty($this->getParams())) {
            $parts[] = http_build_query($this->getParams());
        }

        if ($this->getJsonBody() != null) {
            $parts[] = $this->getJsonBody();
        }

        return implode('&', $parts);
    }

    public function encrypted()
    {
        return hash_hmac('sha256', $this->plaintext(), $this->getApiSecret());
    }

    public function equals($signature)
    {
        return $this->encrypted() == $signature;
    }
}
