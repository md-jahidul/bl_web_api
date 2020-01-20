<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

/**
 * Class PushNotificationService
 * @package App\Services
 */
class IdpIntegrationService
{

    const IDP_TOKEN_REDIS_KEY = "ASSETLITE_IDP_TOKEN";

   // static $token = null;

    /**
     * Get Host from env file
     *
     * @return string
     */
    public static function getHost()
    {
        return config('apiurl.idp_host');
    }

    public static function setToken()
    {
        // get token and set
        $data_param = [
            'grant_type' => 'client_credentials',
            'client_id' => config('apiurl.idp_client_id'),
            'client_secret' => config('apiurl.idp_client_secret'),
            'provider' => 'users'
        ];

        $response = static::post('/oauth/token', $data_param, static::makeHeader(false));

        $response_data = json_decode($response['data']);


        if (isset($response_data->access_token)) {
            // set this token in redis
            Redis::set(self::IDP_TOKEN_REDIS_KEY, $response_data->access_token);
            //
           // static::$token = $response_data->access_token;
        }
    }

    /**
     * Get Token from env file
     *
     * @return string
     */
    public static function getToken()
    {

        if (!Redis::get(self::IDP_TOKEN_REDIS_KEY)) {
             static::setToken();
        }

        return Redis::get(self::IDP_TOKEN_REDIS_KEY);
        
       // if (!static::$token) {
         //   static::setToken();
       // }

       // return static::$token;
        
    }


    /**
     * Send request for user registration
     *
     * @param $data
     * @return string
     */
    public static function registrationRequest($data)
    {
        $res = static::post('/api/customers', $data);

        return $res;
    }


    /**
     * Send request for user login
     *
     * @param $data
     * @return string
     */
    public static function loginRequest($data)
    {
        return static::post('/oauth/token', $data);
    }

    /**
     * Send Request for token validation
     *
     * @param $token
     * @return string
     */
    public static function tokenValidationRequest($token)
    {
        return static::post('/api/v1/check/user/token', $token);
    }


    /**
     * Send request for customer info
     *
     * @param $msisdn
     * @return mixed
     */
    public static function getCustomerInfo($msisdn)
    {
        return static::get('/api/customers/' . $msisdn);
    }


    /**
     * Send request for user login
     *
     * @param $data
     * @return string
     */
    public static function otpGrantTokenRequest($data)
    {
        return static::post('/oauth/token', $data);
    }


    /**
     * Send Request for update password
     *
     * @param $data
     * @return string
     */
    public static function forgetPasswordRequest($data)
    {
        return static::put('/api/customers/forget/password', $data);
    }


    /**
     * Send Request for update password
     *
     * @param $data
     * @return string
     */
    public static function changePasswordRequest($data)
    {
        return static::put('/api/customers/change/password', $data);
    }


    /**
     * Make the header array with authentication.
     *
     * @param bool $isAuthorizationRequired
     * @return array
     */
    private static function makeHeader($isAuthorizationRequired = true)
    {
        $header = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Expect: 100-continue'
        ];

        if ($isAuthorizationRequired) {
            array_push($header, 'Authorization: Bearer ' . static::getToken());
        }
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
    public static function get($url, $body = [], $headers = null)
    {
        return static::makeMethod('get', $url, $body, $headers);
    }

    /**
     * Make CURL request for POST request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    public static function post($url, $body = [], $headers = null)
    {
        return static::makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make CURL request for PUT request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    public static function put($url, $body = [], $headers = [])
    {
        return static::makeMethod('put', $url, $body, $headers);
    }

    /**
     * Make CURL request for DELETE request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string
     */
    public static function delete($url, $body = [], $headers = [])
    {
        return static::makeMethod('delete', $url, $body, $headers);
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
    private static function makeMethod($method, $url, $body = [], $headers = null)
    {
        $ch = curl_init();
        $headers = $headers ?: static::makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        static::makeRequest($ch, $url, $body, $headers);
        $data = curl_exec($ch);

        $info = curl_getinfo($ch);

        if ($info['http_code'] == 401) {
            self::setToken();
            static::makeRequest($ch, $url, $body, $headers);
            $data = curl_exec($ch);
        }

        if ($info['http_code'] == 0) {
            throw new \RuntimeException('Sorry, some problem has occurred unexpectedly');
        }

        $info = curl_getinfo($ch);
        $result = ['data' => $data, 'http_code' => $info['http_code']];

        curl_close($ch);

        return $result;
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
    private static function makeRequest($ch, $url, $body, $headers)
    {
        $url = static::getHost() . $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
}
