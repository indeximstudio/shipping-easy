<?php

namespace Indeximstudio\ShippingEasy;

class PartnerSession extends SEObject
{
    public function create($data = array())
    {
        return $this->request(
            "post",
            "/partners/api/sessions",
            null,
            array("session" => $data),
            ShippingEasy::$partnerApiKey,
            ShippingEasy::$partnerApiSecret
        );
    }
}
