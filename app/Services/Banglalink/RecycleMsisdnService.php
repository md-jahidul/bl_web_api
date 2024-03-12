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
    protected const RECYCLE_MSISDN_CHECK_API_ENDPOINT = "https://jsonplaceholder.typicode.com/posts";

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
            $requestUrl = self::RECYCLE_MSISDN_CHECK_API_ENDPOINT."/".$msisdn;
            $responseData = $this->get($requestUrl);
            // $response = json_decode($responseData['response']);

            if ($responseData['status_code'] == 200){
                $data['is_recycle'] = true;
            } else {
                $data['is_recycle'] = false;
            }
            return $this->responseFormatter->sendSuccessResponse($data, 'Recycle msisdn checked successfully');
        } catch (\Exception $e) {
            return $this->responseFormatter->sendErrorResponse("Something went wrong!", [$e->getMessage()], 500);
        }
    }

}
