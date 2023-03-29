<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;

use App\Exceptions\AmarOfferBuyException;
use App\Exceptions\IdpAuthException;

use App\Repositories\AmarOfferDetailsRepository;
use App\Services\AlBannerService;
use App\Services\ApiBaseService;
use App\Services\CustomerService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AmarOfferService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $amarOfferDetailsRepository;
    public $productService;
    public $responseFormatter;
    protected const AMAR_OFFER_API_ENDPOINT = "/product-offer/offer/amar-offers";
    protected const AMAR_OFFER_API_ENDPOINT_V2 = "/product-offer/offer/v2/amar-offers";

    protected const BANNER_IMAGE = "banner_image";
    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var BanglalinkCustomerService
     */
    private $blCustomerService;
    /**
     * @var AlBannerService
     */
    private $alBannerService;

    public function __construct
    (
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        AmarOfferDetailsRepository $amarOfferDetailsRepository,
        BanglalinkCustomerService $blCustomerService,
        AlBannerService $alBannerService
    ) {
        $this->amarOfferDetailsRepository = $amarOfferDetailsRepository;
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
        $this->alBannerService = $alBannerService;
    }

    public function getAmarOfferListUrl($msisdn, $customerType)
    {
        $channelId = 7;
        $channel = "Website";
        $serviceType = ($customerType == "PREPAID") ? 1 : 2;

        return self::AMAR_OFFER_API_ENDPOINT . "?" . "channel=$channel" . "&channelId=$channelId" . "&msisdn=$msisdn" . "&serviceTypeId=$serviceType";
    }

//    public function getAmarOfferDetailsUrl($msisdn, $offer_id)
//    {
//        return '/product-offer/offer/amar-offer-details' . "?" . "msisdn=$msisdn&offerID=$offer_id";
//    }

    public function getBuyAmarOfferUrl()
    {
        return "/product-offer/offer/purchase-offer";
    }

    public function getBuyAmarOfferUrlV2()
    {
        return "/product-offer/offer/v2/purchase-offer";
    }

    public function prepareAmarOfferList($data)
    {
        $offers = [];
        $sorted_data = collect($data)->sortBy('offerRank');
        foreach ($sorted_data as $offer) {
            if ($data = $this->parseOfferData($offer)) {
                $offers [] = $data;
            }
        }
        return $offers;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAmarOfferList(Request $request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);
        $infoBl = $this->blCustomerService->getCustomerInfoByNumber($customerInfo->msisdn);

        $customerType = $infoBl->getData()->data->connectionType;
        $responseData = $this->get($this->getAmarOfferListUrl(substr($customerInfo->msisdn, 3), $customerType));
//        $bannerImage = $this->amarOfferDetailsRepository
//            ->findOneByProperties(['type' => self::BANNER_IMAGE], ['banner_image_url', 'banner_mobile_view', 'alt_text']);
        if ($responseData['status_code'] == 200){
//            dd($responseData);
            //            $data['header'] = $bannerImage;
            $data = $this->prepareAmarOfferList(json_decode($responseData['response']));
            return $this->responseFormatter->sendSuccessResponse($data, 'Amar Offer List');
        }

        if ($responseData['status_code'] == 500){
            return $this->responseFormatter->sendErrorResponse("Something went wrong!", "Internal Server Error", 500);
        }

    }

    /**
     * @param $offer
     * @param bool $include_details
     * @return array
     */
    private function parseOfferData($offer, $include_details = true)
    {
        $offer_details = [];
        $offer_description = $offer->offerDescriptionWeb;
        $offers = explode(';', $offer_description);
        $offer_details ['offer_id'] = $offer->offerID;

        if ($include_details) {
            $offer_details ['offer_name'] = $offer->offerName;
            $offer_details ['description'] = $offer->offerLongDescription;
        }

        foreach ($offers as $segment) {
            $data = explode('|', $segment);
            $type = $data[0];
            switch ($type) {
                case "VOICE":
                    $offer_details ['minute_volume'] = (int)$data[1];
                    break;
                case "SMS":
                    $offer_details ['sms_volume'] = (int)$data[1];
                    break;
                case "DATA":
                    if (strtolower($data[2]) == 'gb') {
                        $mb = (float)$data[1] * 1024 ;
                    } else {
                        $mb = (float)$data[1];
                    }
                    $offer_details ['internet_volume_mb'] = $mb;
                    break;
                case "TK":
                    $offer_details ['price_tk'] = (int)$data[1];
                    break;
                case "VAL":
                    $valUnit = strtolower($data[2]);
                    if ($valUnit == "hours"){
                        $day = $data[1] / 24;
                        $valUnit = ($day > 1) ? "Days" : "Day";
                    }else{
                        $day = $data[1];
                    }
                    $offer_details ['validity_days'] = (int)$day;
                    $offer_details ['validity_unit'] = ucfirst(strtolower($valUnit));
                    break;
                case "CAT":
                    if ($data[1] == "DAT"){
                        $offerType = "data";
                    } elseif ($data[1] == "VOI") {
                        $offerType = "voice";
                    } elseif ($data[1] == "MIX"){
                        $offerType = "bundles";
                    } else{
                        $offerType = $data[1];
                    }
                    $offer_details['offer_type'] = strtolower($offerType);
//                    $offer_details['offer_details'] = $this->getAmarOfferDetails($offerType);
                    break;
            }
        }
        return $offer_details;
    }

    public function prepareAmarOfferListV2($data)
    {
        $offers = [];
        $collection = collect($data);
        foreach ($collection['data'] as $offer) {
            if ($data = $this->parseOfferDataV2((array)$offer)) {
                $offers [] = $data;
            }
        }
        return $offers;
    }

    /**
     * @param $offer
     * @param bool $include_details
     * @return array
     */
    private function parseOfferDataV2($offer, $include_details = true)
    {
        $offer_details = [];
        $offers = explode(';', $offer['longDescriptionLang1']);
        $offer_details ['offer_id'] = $offer['offercode'];

        if ($include_details) {
            $offer_details ['offer_name'] = $offer['offerName'];
            $offer_details ['short_desc_en'] = $offer['shortDescriptionLang1'];
            $offer_details ['short_desc_bn'] = $offer['shortDescriptionLang2'];
            $offer_details ['treatment_code'] = $offer['treatmentCode'];
            //    $offer_details ['long_desc_bn'] = $offer['longDescriptionLang2'];
        }

        foreach ($offers as $segment) {
            $data = explode('|', $segment);
            $type = $data[0];
            switch ($type) {
                case "VOICE":
                    $offer_details ['minute_volume'] = (int)$data[1];
                    break;
                case "SMS":
                    $offer_details ['sms_volume'] = (int)$data[1];
                    break;
                case "DATA":
                    if (strtolower($data[2]) == 'gb') {
                        $mb = (float)$data[1] * 1024 ;
                    } else {
                        $mb = (float)$data[1];
                    }
                    $offer_details ['internet_volume_mb'] = $mb;
                    break;
                case "TK":
                    $offer_details ['price_tk'] = (int)$data[1];
                    break;
                case "VAL":
                    $valUnit = strtolower($data[2]);
                    if ($valUnit == "hours"){
                        $day = $data[1] / 24;
                        $valUnit = ($day > 1) ? "Days" : "Day";
                    }else{
                        $day = $data[1];
                    }
                    $offer_details ['validity_days'] = (int)$day;
                    $offer_details ['validity_unit'] = ucfirst(strtolower($valUnit));
                    break;
                case "CAT":
                    if ($data[1] == "DAT"){
                        $offerType = "data";
                    } elseif ($data[1] == "VOI") {
                        $offerType = "voice";
                    } elseif ($data[1] == "MIX"){
                        $offerType = "bundles";
                    } else{
                        $offerType = $data[1];
                    }
                    $offer_details['offer_type'] = strtolower($offerType);
//                    $offer_details['offer_details'] = $this->getAmarOfferDetails($offerType);
                    break;
            }
        }
        return $offer_details;
    }

    public function generateRequestID($platform = "")
    {
        $platformPrefix = $platform;
        return uniqid($platformPrefix);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAmarOfferListV2(Request $request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);
        $body = array(
            "channel" => "MYBLAPP",
            "msisdn" => $customerInfo->msisdn,
            "offerSubType" => "ALL",
            "offerType" => "ALL",
            "requestID" => $this->generateRequestID(),
            "serviceType" => ucfirst($customerInfo->number_type)
        );
        $responseData = $this->post(self::AMAR_OFFER_API_ENDPOINT_V2, $body);
        if ($responseData['status_code'] == 200){
            $data = $this->prepareAmarOfferListV2(json_decode($responseData['response']));
            return $this->responseFormatter->sendSuccessResponse($data, 'Amar Offer List');
        }

        return $this->responseFormatter->sendErrorResponse("Something went wrong!", "Internal Server Error", 500);
    }

    public function getAmarOfferDetails($type)
    {
        return $this->amarOfferDetailsRepository->offerDetails($type);
    }

    /**
     * @param $response
     * @return array
     * @throws AmarOfferBuyException
     */
    private function prepareBuyOfferResponse($response)
    {
        if (isset($response->ID)) {
            return [
              'purchase_id' => $response->ID
            ];
        }
        throw new AmarOfferBuyException();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AmarOfferBuyException
     * @throws IdpAuthException
     */
    public function buyAmarOffer(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        $response_data = $this->post($this->getBuyAmarOfferUrl(), [
            'channel' => 'MYBLAPP',
            'channelId' => 7,
            'msisdn'  => substr($customer->msisdn, 3),
            'offerID' => $request->offer_id
        ]);

        $offer_data = json_decode($response_data['response']);
        $formatted_data = $this->prepareBuyOfferResponse($offer_data);

        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'You have successfully purchased offer');
    }

    /**
     * @param $response
     * @return array
     * @throws AmarOfferBuyException
     */
    private function prepareBuyOfferResponseV2($response)
    {
        if ($response['responseCode'] == 0) {
            return [
                'purchase' => 'success',
                'purchase_id' => $response['requestID']
            ];
        }

        return [
            "purchase" => 'failed',
            "message" => $response['responseDesc']
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AmarOfferBuyException
     * @throws IdpAuthException
     */
    public function buyAmarOfferV2(Request $request)
    {
        $customer = $this->customerService->getCustomerDetails($request);

        $treatment_code = $request->input('treatment_code');
        $offerCode = $request->input('offer_id');

        $body = array(
            "requestID" => $this->generateRequestID('PROV'),
            "msisdn" => $customer->msisdn,
            "channel" => "MYBLAPP",
            "treatmentCode"=> $treatment_code,
            "offerCode" => $offerCode
        );

        $response_data = $this->post($this->getBuyAmarOfferUrlV2(), $body);

        if ($response_data['status_code'] == HttpStatusCode::SUCCESS) {
            $offer_data = json_decode($response_data['response']);
            $formatted_data = $this->prepareBuyOfferResponseV2((array) $offer_data);

            if($formatted_data['purchase'] == 'success'){
                return $this->responseFormatter->sendSuccessResponse($formatted_data, 'You have successfully purchased offer');
            }

            return $this->responseFormatter->sendErrorResponse(
                "Amar Offer Purchase Failed",
                $formatted_data,
                400
            );

        } else {
            Log::channel('amarOffer')->info('Amar Offer Request:' . json_encode($body));
            return $this->responseFormatter->sendErrorResponse(
                "Something went wrong",
                [],
                500
            );
        }
    }

    public function getDetails($request, $offerType, $offerId)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);

        $body = array(
            "channel" => "MYBLAPP",
            "msisdn" => $customerInfo->msisdn,
            "offerSubType" => "ALL",
            "offerType" => "ALL",
            "requestID" => $this->generateRequestID(),
            "serviceType" => ucfirst($customerInfo->number_type)
        );
        $response_data = $this->post(self::AMAR_OFFER_API_ENDPOINT_V2, $body);
        $bannerImage = $this->alBannerService->getBanner(0, "amar_offer");

        $data = $this->prepareAmarOfferListV2(json_decode($response_data['response']));
        $offer = collect($data)->where('offer_id', $offerId)->first();
        if ($response_data['status_code'] == 200 && !empty($offer)){
            $details = $this->amarOfferDetailsRepository->offerDetails($offerType)->toArray();
            $data = array_merge($offer, $details);
            $data['banner'] = $bannerImage ?? null;
            return $this->responseFormatter->sendSuccessResponse($data, "Amar Offer details");
        }

        return $this->responseFormatter->sendErrorResponse("Something went wrong!", "Internal Server Error", 500);
    }
}
