<?php

namespace Indeximstudio\ShippingEasy;

use Indeximstudio\ShippingEasy\Exceptions\ApiConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\CurlConnectionException;
use Indeximstudio\ShippingEasy\Exceptions\FailAuthenticationException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidRequestException;
use Indeximstudio\ShippingEasy\Exceptions\InvalidResponseException;

class ApiRequester
{

    /**
     * @param $value
     * @return string
     */
    public static function utf8($value)
    {
        if (is_string($value) && mb_detect_encoding($value, 'UTF-8', true) != 'UTF-8') {
            return utf8_encode($value);
        }

        return $value;
    }

    /**
     * @param $arr
     * @param null $prefix
     * @return string
     */
    public static function encode($arr, $prefix = null)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $r = [];
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            if ($prefix && $k && !is_int($k)) {
                $k = $prefix . "[{$k}]";
            } else if ($prefix) {
                $k = $prefix . '[]';
            }

            if (is_array($v)) {
                $r[] = self::encode($v, $k);
            } else {
                $r[] = urlencode($k) . '=' . urlencode($v);
            }
        }

        return implode('&', $r);
    }

    /**
     * @param $method
     * @param $path
     * @param null $params
     * @param null $payload
     * @param null $apiKey
     * @param null $apiSecret
     * @return mixed
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function request($method, $path, $params = null, $payload = null, $apiKey = null, $apiSecret = null)
    {
        list($rbody, $rcode) = $this->requestRaw($method, $path, $params, $payload, $apiKey, $apiSecret);

        return $this->interpretResponse($rbody, $rcode);
    }

    /**
     * @param $rbody
     * @param $rcode
     * @param $resp
     * @throws ApiConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    public function handleApiError($rbody, $rcode, $resp)
    {

        if (!is_array($resp) || !isset($resp['errors'])) {
            throw new InvalidResponseException(
                "Invalid response body from API: {$rbody} (HTTP response code was {$rcode})",
                502,
                $rbody
            );
        }

        $error = $resp['errors'];
        $message = isset($error[0]['message']) ? $error[0]['message'] : null;

        switch ($rcode) {
            case 400:
                throw new InvalidRequestException();
            case 404:
                throw new InvalidRequestException($message, $rcode, $rbody);
            case 401:
                throw new FailAuthenticationException($message, $rcode, $rbody);
            default:
                throw new ApiConnectionException($message, $rcode);
        }
    }

    /**
     * @param $http_method
     * @param $path
     * @param $params
     * @param $payload
     * @param $apiKey
     * @param $apiSecret
     * @return array
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     */
    private function requestRaw($http_method, $path, $params, $payload, $apiKey, $apiSecret)
    {
        $url = new SignedUrl($http_method, $path, $params, $payload, null, $apiKey, $apiSecret);
        $absUrl = $url->toString();
        $uname = php_uname();
        $ua = [
            'bindings_version' => ShippingEasy::VERSION,
            'lang' => 'php',
            'lang_version' => PHP_VERSION,
            'publisher' => 'ShippingEasy',
            'uname' => $uname
        ];
        $headers = [
            'X-ShippingEasy-Client-User-Agent: ' . json_encode($ua),
            'User-Agent: ShippingEasy/v1 PhpBindings/' . ShippingEasy::VERSION,
            'Authorization: Bearer ' . $apiKey
        ];
        if (ShippingEasy::$apiVersion) {
            $headers[] = 'ShippingEasy-Version: ' . ShippingEasy::$apiVersion;
        }
        list($rbody, $rcode) = $this->curlRequest($http_method, $absUrl, $headers, $payload);

        return [$rbody, $rcode];
    }

    /**
     * @param $rbody
     * @param $rcode
     * @return mixed
     * @throws ApiConnectionException
     * @throws FailAuthenticationException
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     */
    private function interpretResponse($rbody, $rcode)
    {
        try {
            $resp = json_decode($rbody, true);
        } catch (\Exception $e) {
            throw new InvalidResponseException(
                "Invalid response body from API: {$rbody} (HTTP response code was {$rcode})",
                502,
                $rbody
            );
        }
        if ($rcode < 200 || $rcode >= 300) {
            $this->handleApiError($rbody, $rcode, $resp);
        }

        return $resp;
    }

    /**
     * @param $meth
     * @param $absUrl
     * @param $headers
     * @param $payload
     * @return array
     * @throws ApiConnectionException
     * @throws CurlConnectionException
     */
    private function curlRequest($meth, $absUrl, $headers, $payload)
    {
        $curl = curl_init();
        $meth = strtolower($meth);
        $opts = array();

        if ($meth == 'get') {
            $opts[CURLOPT_HTTPGET] = 1;
        } else if ($meth == 'post') {
            $opts[CURLOPT_POST] = 1;
            if ($payload) {
                $payload = json_encode($payload);
            }
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($payload);
            $opts[CURLOPT_POSTFIELDS] = $payload;
        } else if ($meth == 'put') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if ($payload) {
                $payload = json_encode($payload);
            }

            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($payload);
            $opts[CURLOPT_POSTFIELDS] = $payload;
        } else if ($meth == 'delete') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            if (count($params) > 0) {
                $encoded = self::encode($params);
                $absUrl = "{$absUrl}?{$encoded}";
            }
        } else {
            throw new ApiConnectionException("Unrecognized method {$meth}", 505);
        }

        $opts[CURLOPT_URL] = $absUrl;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_CONNECTTIMEOUT] = 30;
        $opts[CURLOPT_TIMEOUT] = 80;
        $opts[CURLOPT_FOLLOWLOCATION] = true;
        $opts[CURLOPT_MAXREDIRS] = 4;
        $opts[CURLOPT_POSTREDIR] = 1 | 2 | 4; // Maintain method across redirect for all 3XX redirect types
        $opts[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($curl, $opts);
        $rbody = curl_exec($curl);

        if ($rbody === false) {
            $errno = curl_errno($curl);
            $message = curl_error($curl);
            curl_close($curl);
            $this->handleCurlError($errno, $message);
        }
        $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [$rbody, $rcode];
    }

    /**
     * @param $errno
     * @param $message
     * @throws CurlConnectionException
     */
    public function handleCurlError($errno, $message)
    {
        $apiBase = ShippingEasy::$apiBase;
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = "Could not connect to ShippingEasy ({$apiBase}).  Please check your internet connection and try again.  If this problem persists, let us know at support@shippingeasy.com.";
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify ShippingEasy's SSL certificate.  Please make sure that your network is not intercepting certificates.  (Try going to $apiBase in your browser.)  If this problem persists, let us know at support@shippingeasy.com.";
                break;
            default:
                $msg = 'Unexpected error communicating with ShippingEasy.  If this problem persists, let us know at support@shippingeasy.com.';
        }

        $msg .= "(Network error {$errno}: {$message})";

        throw new CurlConnectionException($msg);
    }
}
