<?php

namespace App\Services\Payment;
use App\Services\Banglalink\curl_init;
use Illuminate\Support\Facades\Redis;

class PaymentBaseService
{

    const IDP_TOKEN_REDIS_KEY = "ASSETLITE_IDP_TOKEN";

    /**
     * Return BL API Host
     *
     * @return mixed
     */
    protected function getHost()
    {
        return config('apiurl.bl_api_host');
    }

    /**
     * Make the header array with authentication.
     *
     * @return array
     */
    protected function makeHeader()
    {
        $header = [
            'Content-Type' => 'application/json',
            'Accept'     => 'application/json',
        ];

        return $header;
    }


    /**
     * Make CURL request for GET request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    protected function get($baseUrl, $url, $body = [], $headers = null)
    {
        return $this->makeMethod($baseUrl,'get', $url, $body, $headers);
    }

    /**
     * Make CURL request for POST request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    protected function post($url, $body = [], $headers = null)
    {
        return $this->makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    protected function put($url, $body = [], $headers = [])
    {
        return $this->makeMethod('put', $url, $body, $headers);
    }

    /**
     * @param $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    protected function delete($url, $body = [], $headers = [])
    {
        return $this->makeMethod('delete', $url, $body, $headers);
    }

    /**
     * Make CURL request for a HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    protected function makeMethod($baseUrl, $method, $url, $body = [], $headers = null)
    {
        $ch = curl_init();
        $headers = $headers ?: $this->makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        static::makeRequest($ch, $baseUrl, $url, $body, $headers);
        $result = curl_exec($ch);
        //dd($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        dd($result);
        return ['response' => $result, 'status_code' => $httpCode];
    }


    /**
     * Make CURL object for HTTP request verbs.
     *
     * @param curl_init() $ch
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    protected function makeRequest($ch, $baseUrl, $url, $body, $headers)
    {
        $url = $baseUrl . $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
}
