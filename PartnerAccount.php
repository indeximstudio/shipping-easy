<?php

namespace Indeximstudio\ShippingEasy;

class PartnerAccount extends SEObject
{
    public function create($data = array())
    {
        return $this->request(
            "post",
            "/partners/api/accounts",
            null,
            array("account" => $data),
            ShippingEasy::$partnerApiKey,
            ShippingEasy::$partnerApiSecret
        );
    }
}
