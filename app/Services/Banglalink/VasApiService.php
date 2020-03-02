<?php

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use Illuminate\Http\JsonResponse;

class VasApiService extends VasBaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;

    protected const SUCCESS = "00";

    public function __construct(ApiBaseService $apiBaseService) {
        $this->responseFormatter = $apiBaseService;
    }

    public function getCheckSubStatusUrl($providerUrl)
    {
        return "/$providerUrl/checkSubStatus";
    }

    public function subscriptionUrl($providerUrl)
    {
        return "/$providerUrl/subscription";
    }


    /**
     * @param $data
     * @return JsonResponse|mixed
     */
    public function checkSubStatus($data)
    {
        $response_data = $this->post($this->getCheckSubStatusUrl("cinespot"), [
            'msisdn'  => $data['msisdn'],
        ]);
        $statusResponse = json_decode($response_data['response']);
        if ($statusResponse->errorCode == self::SUCCESS){
            return $this->responseFormatter->sendSuccessResponse($statusResponse->data, 'Subscribe Status!');
        }
        return $this->responseFormatter->sendErrorResponse($statusResponse);
    }

    /**
     * @param $data
     * @return JsonResponse|mixed
     */
    public function subscribe($data)
    {
        $response_data = $this->post($this->subscriptionUrl($data['provider_url']), [
            'msisdn'  => $data['msisdn'],
            'pack' => $data['validity_unit']
        ]);
        $statusResponse = json_decode($response_data['response']);
        if ($statusResponse->errorCode == self::SUCCESS){
            return $this->responseFormatter->sendSuccessResponse($statusResponse->data, 'Subscribe Successfully!');
        }
        return $this->responseFormatter->sendErrorResponse($statusResponse);
    }
}
