<?php

namespace Indeximstudio\ShippingEasy;

use Indeximstudio\ShippingEasy\Exceptions\ApiConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\CurlConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\FailAuthenticationException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidRequestException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidResponseException;

class Order extends ApiRequester
{
    private $storeApiKey;
    private $values;

    public function __construct($storeApiKey = null, $values = null)
    {
        $this->storeApiKey = $storeApiKey;
        $this->values = $values;
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
            "/api/stores/{$this->storeApiKey}/orders",
            null,
            ['order' => $this->values]
        );
    }

    /**
     * @param $externalOrderId
     * @param $recipientData
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function updateRecipient($externalOrderId, $recipientData)
    {
        return $this->request(
            'put',
            "/api/stores/{$this->storeApiKey}/orders/{$externalOrderId}/recipient",
            null,
            ['recipient' => $recipientData]
        );
    }

    /**
     * @param $externalOrderId
     * @param $newStatus
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function updateStatus($externalOrderId, $newStatus)
    {
        return $this->request(
            'put',
            "/api/stores/{$this->storeApiKey}/orders/{$externalOrderId}/status",
            null,
            ['order' => ['order_status' => $newStatus]]
        );
    }

    /**
     * @param $id
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function find($id)
    {
        return $this->request('get', "/api/orders/{$id}");
    }

    /**
     * @param $externalOrderId
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function findByStore($externalOrderId)
    {
        return $this->request('get', "/api/stores/{$this->storeApiKey}/orders/{$externalOrderId}");
    }

    /**
     * @param array $params
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function findAllByStore($params = [])
    {
        return $this->request('get', "/api/stores/{$this->storeApiKey}/orders", $params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function findAll($params = [])
    {
        return $this->request('get', '/api/orders', $params);
    }
}
