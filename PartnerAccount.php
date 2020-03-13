<?php

namespace Indeximstudio\ShippingEasy;

use Indeximstudio\ShippingEasy\Exceptions\ApiConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\CurlConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\FailAuthenticationException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidRequestException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidResponseException;

class PartnerAccount extends ApiRequester
{
    /**
     * @param array $data
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function create($data = array())
    {
        return $this->request(
            'post',
            '/partners/api/accounts',
            null,
            ['account' => $data],
            ShippingEasy::$partnerApiKey,
            ShippingEasy::$partnerApiSecret
        );
    }
}
