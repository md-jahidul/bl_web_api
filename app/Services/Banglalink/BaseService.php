<?php

namespace App\Services\Banglalink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BaseService
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
        $client_token = Redis::get(self::IDP_TOKEN_REDIS_KEY);
        $customer_token = app('request')->bearerToken();

        $header = [
            'Accept: application/vnd.banglalink.apihub-v1.0+json',
            'Content-Type: application/vnd.banglalink.apihub-v1.0+json',
            'accept: application/json',
            'client_authorization:' . $client_token,
            'customer_authorization:' . $customer_token
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
    protected function get($url, $body = [], $headers = null)
    {
        return $this->makeMethod('get', $url, $body, $headers);
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
    protected function makeMethod($method, $url, $body = [], $headers = null)
    {
        $ch = curl_init();
        $headers = $headers ?: $this->makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        static::makeRequest($ch, $url, $body, $headers);
        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $url = $this->getHost() . $url;

        if ($httpCode != 200){
            $requestData = [
                'request' => [
                    'api_hub_url' => $url,
                    'body' => $body,
                    'client_url' => request()->getRequestUri()
                ],
                'response' => [
                    'response_data' => $result,
                    'status_code' => $httpCode
                ]
            ];
            Log::channel('apiHubReqError')->info(json_encode($requestData));
        }

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
    protected function makeRequest($ch, $url, $body, $headers)
    {
        $url = $this->getHost() . $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
}
