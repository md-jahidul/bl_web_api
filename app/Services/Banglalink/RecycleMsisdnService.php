<?php

namespace App\Services\Banglalink;

use App\Services\ApiBaseService;
use Illuminate\Http\JsonResponse;

class RecycleMsisdnService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $responseFormatter;
    protected const RECYCLE_MSISDN_CHECK_API_ENDPOINT = "/customer-information-ext/customer-information-ext/sim-recycle/search?msisdn=";

    public function __construct
    (
        ApiBaseService $apiBaseService
    ) {
        $this->responseFormatter = $apiBaseService;
    }


    /**
     * Check Recycle MSISDN service availability
     * 
     * @param int $msisdn
     * @return JsonResponse
     */
    public function checkRecycleMsisdn($msisdn="")
    {
        $data['is_recycle'] = false;
        try {
            $msisdn = $msisdn !== "" ? $msisdn : "1"; // TODO: MSISDN Validation
            $requestUrl = self::RECYCLE_MSISDN_CHECK_API_ENDPOINT.$msisdn;
            $responseData = $this->get($requestUrl);
            $response = json_decode($responseData['response']);
            $data['is_recycle'] = false;
            if ($response->data && $response->data->status){
                $data['is_recycle'] = $response->data->status == "false" ? false : true;
            }
            $message = $response->data->message ?? "";
            return $this->responseFormatter->sendSuccessResponse($data, $message);
        } catch (\Exception $e) {
            return $this->responseFormatter->sendErrorResponse("Something went wrong!", [$e->getMessage()], 500);
        }
    }

}
