<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Exceptions\BLServiceException;
use App\Exceptions\CurlRequestException;
use App\Exceptions\PriyojonException;
use App\Models\Customer;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class PriyojonService
 * @package App\Services\Banglalink
 */
class PriyojonService extends BaseService
{

    /**
     * @var ApiBaseService
     */
    protected $apiBaseService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var BanglalinkCustomerService
     */
    protected $blCustomerService;

    protected const PRIYOJON_ENDPOINT = "/loyalty/loyalty/";


    /**
     * PriyojonService constructor.
     * @param  ApiBaseService  $apiBaseService
     * @param  CustomerService  $customerService
     * @param  BanglalinkCustomerService  $blCustomerService
     */
    public function __construct(
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        BanglalinkCustomerService $blCustomerService
    ) {
        $this->apiBaseService = $apiBaseService;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
    }

    /**
     * Request for getting Priyojon status
     *
     * @param  Request  $request
     * @return string
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function getPriyojonPoints(Request $request)
    {
        $customer = $this->customerService->getAuthenticateCustomer($request);

        if (!$customer) {
            return $this->apiBaseService->sendErrorResponse("Token Invalid", [], HttpStatusCode::UNAUTHORIZED);
        }

        $mobile = "88" . $customer->phone;
        $customer_info = $this->blCustomerService->getCustomerInfoByNumber($mobile);

        if ($customer_info->getData()->status == "FAIL") {
            return $this->apiBaseService->sendErrorResponse(
                "Internal server error",
                [],
                HttpStatusCode::INTERNAL_ERROR
            );
        }

        $customer_id = $customer_info->getData()->data->package->customerId;

        $end_point = self::PRIYOJON_ENDPOINT . "/priyojon-status?customerId=1926800014";

        $result = $this->get($end_point);


        if ($result['status_code'] == 200) {
            $data = $this->getFormattedData($result['response']);

            return $this->apiBaseService->sendSuccessResponse(
                $data,
                "Priyojon Points",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }


    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function getPriyojonStatus(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->apiBaseService->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customerId = substr($user->msisdn,3);
        $end_point = self::PRIYOJON_ENDPOINT . "/priyojon-status?customerId=" . $customerId;

        $result = $this->get($end_point);

        if ($result['status_code'] == 200) {
            $response = json_decode($result['response'], true);

            $data = null;
            $messge_code = $response ['messgeCode'];

            if ($messge_code != "ERR") {
                if(isset($response['data']['priyojonInfo']['slab'])){
                    $data = [
                        'slab' => $response['data']['priyojonInfo']['slab'] ?? "",
                        'available_point' => $response['data']['priyojonInfo']['points']
                    ];
                } else{
                    $data = null;
                }

            }

            return $this->apiBaseService->sendSuccessResponse($data, 'Priyojon status and point');
        }

        return $this->apiBaseService->sendErrorResponse(
            "Currently Service Unavailable. Try again Later",
            [
                'message' => "Currently Service Unavailable. Try again Later"
            ],
            HttpStatusCode::INTERNAL_ERROR
        );
    }
    public function priyojonStatus($msisdn)
    {
        $end_point = self::PRIYOJON_ENDPOINT . "/priyojon-status?customerId=" . $msisdn;

        $result = $this->get($end_point);

        if ($result['status_code'] == 200) {
            $response = json_decode($result['response'], true);

            $messge_code = $response ['messgeCode'];
            if ($messge_code != "ERR") {
                if (isset($response['data']['priyojonInfo']['slab'])) {
                    return strtolower($response['data']['priyojonInfo']['slab']);
                } else {
                    return null;
                }
            }

            return null;
        }

        return null;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws PriyojonException
     * @throws TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function getPriyojonPointHistory(Request $request)
    {
        $startDate = null;
        $endDate = null;
        $methodOneResult = array();
        $methodTwoResult = array();
        if ($request->has('from')) {
            $startDate = $request->input('from') . ' 00:00:00';
        } else {
            $startDate = Carbon::today()->subDay(10)->format('Y-m-d h:i:s');;
        }
        if ($request->has('to')) {
            $endDate = $request->input('to') . ' 23:59:59';
        } else {
            $endDate = Carbon::today()->format('Y-m-d h:i:s');;
        }
        $user = $this->customerService->getAuthenticateCustomer($request);
        if (!$user) {
            return $this->apiBaseService->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }
        // $customerId ='1962424630';
        $customerId = substr($user->msisdn, 3);
        // earn point
        $end_point = self::PRIYOJON_ENDPOINT . "/point-receival-history?customerId=" . $customerId;
        // Priyojon point redeem history
        $end_point2 = self::PRIYOJON_ENDPOINT . "/priyojon-point-redeem-history?customerId=" . $customerId;

        $methodOne = $this->get($end_point);
        $methodTwo = $this->get($end_point2);
        if ($methodOne['status_code'] == 200 || $methodTwo['status_code'] == 200) {
            $methodOneResponse = json_decode($methodOne['response'], true);
            if ($methodOneResponse['messgeCode'] == 200 && $methodOneResponse['message'] == "OK") {
                if (!empty($methodOneResponse['data'])) {
                    $methodOneResult = $this->getFormattedPointsHistoryData($methodOneResponse);
                }
            }
            $methodTwoResponse = json_decode($methodTwo['response'], true);
            if ($methodTwoResponse['messgeCode'] == 200 && $methodTwoResponse['message'] == "OK") {
                if (!empty($methodTwoResponse['data'])) {
                    $methodTwoResult = $this->getFormattedPriyojonPointRedeemHistoryData($methodTwoResponse);
                }
            }
            $totalData = array_merge($methodOneResult, $methodTwoResult);
            $collection = collect($totalData)->whereBetween('earn_date', [$startDate, $endDate])->sortBy('earn_date');
            $mergeData = $collection->all();
            $resultData = self::PriyojonPointHistoryArrayFormat($mergeData);

            return $this->apiBaseService->sendSuccessResponse(
                $resultData,
                "Priyojon Points History",
                [],
                HttpStatusCode::SUCCESS
            );
            throw new PriyojonException($response);
        }
        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }

    public function PriyojonPointHistoryArrayFormat($mergeData)
    {
        $slId = 1;
        $result = array();
        if (!empty($mergeData)) {
            foreach ($mergeData as $key => $value) {
                $result[] = [
                    "id" => $slId++,
                    "title" => $value['title'],
                    "earn_date" => $value['earn_date'],
                    "expire_date" => $value['expire_date'],
                    "points" => $value['points'],
                    "is_earned" => $value['is_earned'],

                ];;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return array
     */
    public function getFormattedPriyojonPointRedeemHistoryData($data)
    {

        $history = [];

        if (!isset($data['data'])) {
            return $history;
        }

        foreach ($data['data'] as $key => $value) {
            if ($value['deliveryStatus'] !== "delivered") {
                $status = true;
            } else {
                $status = false;
            }

            $history[$key] = [
                "id" => $key + 1,
                "title" => $value['offerName'],
                "earn_date" => $value['deliveryDate'],
                "expire_date" => $value['eventDate'],
                "points" => $value['offerPrice'],
                "is_earned" => $status,

            ];
        }

        return $history;

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPriyojonPartnerCategoryList(Request $request)
    {
        $category=[
            "Talk-time Offers",
            "Internet Offers",
            "Physical Gift",
            "Food and Beverage",
            "Fashion and Lifestyle",
            "Tours and Travel",
            "Electronics and Furniture",
            "Health and Beauty Care"
        ];

        foreach ($category as $key => $item) {
            $list[$key] = [
                "id" => $key + 1,
                "icon_url" => null,
                "title" => $item

            ];
        }

        return $this->apiBaseService->sendSuccessResponse(
            $list,
            "Category List",
            [],
            HttpStatusCode::SUCCESS
        );

    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function getPriyojonPartnerList(Request $request)
    {

        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->apiBaseService->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customerId = substr($user->msisdn,3);

        $end_point = self::PRIYOJON_ENDPOINT . "/get-priyojon-redeem-options?msisdn=" . $customerId;

        $result = $this->get($end_point);

        if ($result['status_code'] == 200) {

            $response = json_decode($result['response'], true);

            if($response['messgeCode'] == "ERR") {
                return $this->apiBaseService->sendErrorResponse(
                    "No offers are available now. Please retry later",
                    [],
                    HttpStatusCode::NOT_FOUND
                );
            }


            $category = $request->category;

            $partnerList = $this->filterItems($category, $response);

            $data = $this->getFormattedPartnerListData($partnerList);

            return $this->apiBaseService->sendSuccessResponse(
                $data,
                "Priyojon Partner List",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function getPriyojonTelcoOfferList(Request $request)
    {

        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->apiBaseService->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

        $customerId = substr($user->msisdn,3);


        //$customerId=1903303978;

        $end_point = self::PRIYOJON_ENDPOINT . "/get-priyojon-redeem-options?msisdn=" . $customerId;

        $result = $this->get($end_point);


        if ($result['status_code'] == 200) {

            $response = json_decode($result['response'], true);

            if($response['messgeCode'] == "ERR") {
                return $this->apiBaseService->sendErrorResponse(
                    "No offers are available now. Please retry later",
                    [],
                    HttpStatusCode::NOT_FOUND
                );
            }


            $category = $request->category;

            $offerList = $this->filterItems($category, $response);

            $data = $this->getFormattedOfferListData($offerList);

            return $this->apiBaseService->sendSuccessResponse(
                $data,
                "Priyojon Telco offer",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BLServiceException
     * @throws CurlRequestException
     * @throws \App\Exceptions\TokenInvalidException
     * @throws \App\Exceptions\TokenNotFoundException
     * @throws \App\Exceptions\TooManyRequestException
     */
    public function purchaseRedeemPoint(Request $request)
    {
        $user = $this->customerService->getAuthenticateCustomer($request);

        if (!$user) {
            return $this->apiBaseService->sendErrorResponse("User not found", [], HttpStatusCode::UNAUTHORIZED);
        }

       $data = $request->all();

       $param = [
           "msisdn" => substr($data['mobile'],1),
           "offerId" => $data['product_code']
       ];

        $end_point = self::PRIYOJON_ENDPOINT . "/purchase-priyojon-redeem";

        $result = $this->post($end_point, $param);

        if ($result['status_code'] == 200) {

            $response = json_decode($result['response'], true);

            if($response['messgeCode'] == "ERR") {
                return $this->apiBaseService->sendErrorResponse(
                    "Something went wrong!. Please retry later",
                    [],
                    HttpStatusCode::NOT_FOUND
                );
            }


            return $this->apiBaseService->sendSuccessResponse(
                $response,
                "Purchase redeem Points Successfully",
                [],
                HttpStatusCode::SUCCESS
            );
        }

        return $this->apiBaseService->sendErrorResponse(
            "Internal server error",
            [],
            HttpStatusCode::INTERNAL_ERROR
        );
    }

    /**
     * @param $data
     * @return mixed
     */
    public function getFormattedPointsHistoryData($data)
    {

        $history=[];

        if(!isset($data['data'] )){
           return $history;
        }

        foreach ($data['data'] as $key => $value)
        {
            if($value['deliveryStatus'] == "delivered"){
                $status = true;
            } else{
                $status = false;
            }
            $history[$key] = [
                "id"=> $key+1,
                "title"=> $value['deliverableName'],
                "earn_date"=> $value['deliveryDate'],
                "expire_date"=> $value['deliverableExpirationDate'],
                "points"=> $value['deliverableQty'],
                "is_earned"=> $status

            ];
        }

        return $history;

    }

    /**
     * @param $partner
     * @return mixed
     */
    private function getFormattedPartnerListData($partner)
    {
        $index = 0;
        $formattedPartnerList=[];
        $telcoOffers = config('constants.priyojon_telco_offers');

        foreach ($partner as $key => $value)
        {
            if($value['partnerName'] == "NA" || in_array($value['offerCategoryName'], $telcoOffers))
            {
                continue;
            }

            $formattedPartnerList[$index] = [
                "id"=> $value['offerID'],
                "image_url"=> $value['imageURL'],
                "title"=> $value['partnerName'],
                "subtitle"=> $value['offerShortDescription'],
                "details"=> $value['offerDescriptionWeb'],
            ];

            $index++;
        }

        return $formattedPartnerList;
    }

    /**
     * @param $item
     * @return array
     */
    private function getFormattedOfferListData($item)
    {
        $offer=[];
        foreach ($item as $key => $value)
        {
            $main = explode(";", $value['offerDescriptionWeb']);


            if(isset($main[0])){
                $voice = explode("|", $main[0]);
                $min =  $voice[1] ?? null;
            }

            if(isset($main[1])){
                $sms_arr = explode("|", $main[1]);
                $sms = $sms_arr[1] ?? null;
            }

            if(isset($main[2])){
                $internet = explode("|", $main[2]);
                $dataUnit = $internet[2] ?? null;
                $net = ($dataUnit != 'MB' ?  ($internet[1] * 1024) : $internet[1]) ?? null;
            }

            if(isset($main[4])){
                $point = explode("|", $main[4]);
                $pnt = $point[1] ?? null;
            }

            if(isset($main[5])){
                $validity = explode("|", $main[5]);
                $val = $validity[1] ?? null;
                $unit = $validity[2] ?? null;
            }

            $offer[$key] = [
                "id"=> $value['offerID'],
                "internet"=> (double) $net ?? null,
                "sms"=>  (int) $sms ?? null,
                "minutes"=> (double) $min ?? null,
                "validity"=> (int) $val ?? null,
                "validity_unit"=> $unit ?? null,
                "tag"=> $value['offerCategoryCode'],
                "points"=> (int) $pnt ?? null,
                "product_code"=> $value['offerID'],
                "details" => $value['offerLongDescription'] ?? ""
            ];
        }

        return $offer;
    }

    /**
     * @param $category
     * @param $response
     * @return array
     */
    public function filterItems($category, $response)
    {
        if ($category) {
            $array = $response['data'];

            $filteredArray = Arr::where($array, function ($value, $key) use ($category) {
                return $value['offerCategoryName'] == $category;
            });

            return $filteredArray;

        } else {

            return $response['data'];
        }
    }

    public function priyojonUsageHistoryTotal($customer_id, $from, $to, $orange_points = null)
    {
        $startDate = null;
        $endDate = null;
        $redeemHistoryPoint = 0;
        $receivalHistoryPoint = 0;
        $receivalHistory = array();
        $redeemHistory = array();
        if (!empty($from)) {
            $startDate = $from . ' 00:00:00';
        } else {
            $startDate = Carbon::today()->subDay(10)->format('Y-m-d h:i:s');;
        }
        if (!empty($to)) {
            $endDate = $to . ' 23:59:59';
        } else {
            $endDate = Carbon::today()->format('Y-m-d h:i:s');;
        }
        $customerId = $customer_id;
        // earn point
        $end_point = self::PRIYOJON_ENDPOINT . "/point-receival-history?customerId=" . $customerId;
        // Priyojon point redeem history
        $end_point2 = self::PRIYOJON_ENDPOINT . "/priyojon-point-redeem-history?customerId=" . $customerId;

        $methodOne = $this->get($end_point);
        $methodTwo = $this->get($end_point2);
        if ($methodOne['status_code'] == 200 || $methodTwo['status_code'] == 200) {
            $methodOneResponse = json_decode($methodOne['response'], true);
            if ($methodOneResponse['messgeCode'] == 200 && $methodOneResponse['message'] == "OK") {

                if (!empty($methodOneResponse['data'])) {
                    $receivalHistory = $this->getFormattedPointsHistoryData($methodOneResponse);
                    $receivalHistoryPoint = collect($receivalHistory)->whereBetween('earn_date', [$startDate, $endDate])->sum('points');
                }
            }

            $methodTwoResponse = json_decode($methodTwo['response'], true);
            if ($methodTwoResponse['messgeCode'] == 200 && $methodTwoResponse['message'] == "OK") {
                if (!empty($methodTwoResponse['data'])) {
                    $redeemHistory = $this->getFormattedPriyojonPointRedeemHistoryData($methodTwoResponse);
                    $redeemHistoryPoint = collect($redeemHistory)->whereBetween('earn_date', [$startDate, $endDate])->sum('points');
                }
            }
            $totalPoint['orange_points'] = $receivalHistoryPoint - $redeemHistoryPoint;
            return $totalPoint;
        }
        $totalPoint['orange_points'] = 0;
        return $totalPoint;
    }

}

