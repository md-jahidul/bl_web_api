<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/27/19
 * Time: 12:00 PM
 */

namespace App\Services\Payment;


use App\Enums\HttpStatusCode;
use App\Services\ApiBaseService;
use App\Services\NumberValidationService;
use Illuminate\Support\Facades\Redis;

class PaymentGatewaysService extends ApiBaseService
{
    /**
     * @var NumberValidationService
     */
    protected $numberValidationService;

    /**
     * SslCommerzService constructor.
     * @param NumberValidationService $numberValidationService
     */
    public function __construct(
//        NumberValidationService $numberValidationService
    ) {
//        $this->numberValidationService = $numberValidationService;
    }

    public function paymentGateways()
    {
        return $this->sendSuccessResponse();
    }

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
     * Make CURL request for a HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
     */
    protected function makeMethod($method, $url, $body = [], $headers = null)
    {
        $ch = curl_init();
        $headers = $headers ?: $this->makeHeader();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        static::makeRequest($ch, $url, $body, $headers);
        $result = curl_exec($ch);
        //dd($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return ['response' => $result, 'status_code' => $httpCode];
    }

    /**
     * Make CURL request for GET request.
     *
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return array
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
     * @return array
     */
    protected function post($url, $body = [], $headers = null)
    {
        return $this->makeMethod('post', $url, $body, $headers);
    }

    /**
     * Make the header array with authentication.
     *
     * @return array
     */
    protected function makeHeader()
    {
//        $client_token = Redis::get(self::IDP_TOKEN_REDIS_KEY);
        $customer_token = app('request')->bearerToken();

        $header = [
            'Accept: application/vnd.banglalink.apihub-v1.0+json',
            'Content-Type: application/vnd.banglalink.apihub-v1.0+json',
            'accept: application/json',
            'client_authorization:' ,
            'customer_authorization:' . $customer_token
        ];

        return $header;
    }
}
