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

    public function cancelSubscriptionUrl($providerUrl)
    {
        return "/$providerUrl/cancelSubscription";
    }

    public function contentListUrl($providerUrl)
    {
        return "/$providerUrl/contentList";
    }

    public function contentDetailUrl($providerUrl)
    {
        return "/$providerUrl/contentDetail";
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
            return $this->responseFormatter->sendSuccessResponse($statusResponse->data, 'Subscribe successfully!');
        }
        return $this->responseFormatter->sendErrorResponse($statusResponse);
    }

    public function cancelSubscription($data)
    {
        $response_data = $this->post($this->cancelSubscriptionUrl($data['provider_url']), [
            'msisdn'  => $data['msisdn'],
        ]);
        $statusResponse = json_decode($response_data['response']);
        if ($statusResponse->errorCode == self::SUCCESS){
            return $this->responseFormatter->sendSuccessResponse($statusResponse->data, 'Subscribe cancel successfully!');
        }
        return $this->responseFormatter->sendErrorResponse($statusResponse);
    }

    /**
     * @param $providerUrl
     * @return JsonResponse|mixed
     */
    public function contentList($providerUrl)
    {
        $response_data = $this->get($this->contentListUrl($providerUrl));
        $statusResponse = json_decode($response_data['response']);
        if ($statusResponse->errorCode == self::SUCCESS){
            return $this->responseFormatter->sendSuccessResponse($statusResponse->data, 'Subscribe cancel successfully!');
        }
        return $this->responseFormatter->sendErrorResponse($statusResponse);
    }

    public function contentDetail($providerUrl, $contentId)
    {
        //
    }

}
