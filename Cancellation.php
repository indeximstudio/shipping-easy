<?php

namespace Indeximstudio\ShippingEasy;

use Indeximstudio\ShippingEasy\Exceptions\ApiConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\CurlConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\FailAuthenticationException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidRequestException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidResponseException;

class Cancellation extends ApiRequester
{
    private $storeApiKey;
    private $externalOrderIdentifier;

    public function __construct($storeApiKey, $externalOrderIdentifier)
    {
        $this->storeApiKey = $storeApiKey;
        $this->externalOrderIdentifier = $externalOrderIdentifier;
    }

    /**
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function create()
    {
        return $this->request(
            'post',
            "/api/stores/{$this->storeApiKey}/orders/{$this->externalOrderIdentifier}/cancellations"
        );
    }

}
