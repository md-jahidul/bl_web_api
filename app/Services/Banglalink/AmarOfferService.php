<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;

use App\Exceptions\AmarOfferBuyException;
use App\Exceptions\IdpAuthException;

use App\Repositories\AmarOfferDetailsRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmarOfferService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $amarOfferDetailsRepository;
    public $productService;
    public $responseFormatter;
    protected const AMAR_OFFER_API_ENDPOINT = "/product-offer/offer/amar-offers";

    protected const BANNER_IMAGE = "banner_image";
    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var BanglalinkCustomerService
     */
    private $blCustomerService;

    public function __construct
    (
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        AmarOfferDetailsRepository $amarOfferDetailsRepository,
        BanglalinkCustomerService $blCustomerService

    ) {
        $this->amarOfferDetailsRepository = $amarOfferDetailsRepository;
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
        $this->blCustomerService = $blCustomerService;
    }

    public function getAmarOfferListUrl($msisdn, $customerType)
    {
        $channelId = 7;
        $channel = "Website";
        $serviceType = ($customerType == "PREPAID") ? 1 : 2;

        return self::AMAR_OFFER_API_ENDPOINT . "?" . "channel=$channel" . "&channelId=$channelId" . "&msisdn=$msisdn" . "&serviceTypeId=$serviceType";
    }

    public function getAmarOfferDetailsUrl($msisdn, $offer_id)
    {
        return '/product-offer/offer/amar-offer-details' . "?" . "msisdn=$msisdn&offerID=$offer_id";
    }

    public function getBuyAmarOfferUrl()
    {
        return "/product-offer/offer/purchase-offer";
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
                        $mb = (int)$data[1] * 1024 ;
                    } else {
                        $mb = (int)$data[1];
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

                    $offer_details['offer_details'] = $this->getAmarOfferDetails($offerType);
                    break;
            }
        }

        return $offer_details;
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
        $customer_type = $infoBl->getData()->data->connectionType;
        $response_data = $this->get($this->getAmarOfferListUrl(substr($customerInfo->msisdn, 3), $customer_type));
        $bannerImage = $this->amarOfferDetailsRepository
            ->findOneByProperties(['type' => self::BANNER_IMAGE], ['banner_image_url', 'banner_mobile_view', 'alt_text']);

        if ($response_data['status_code'] == 200){
            $formatted_data = $this->prepareAmarOfferList(json_decode($response_data['response']));
            $data['header'] = $bannerImage;
            $data['offers'] = $formatted_data;
            return $this->responseFormatter->sendSuccessResponse($data, 'Amar Offer List');
        }

        if ($response_data['status_code'] == 500){
            return $this->responseFormatter->sendErrorResponse("Something went wrong!", "Internal Server Error", 500);
        }

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
        if (isset($response->Status) && $response->Status == 'success') {
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
            'msisdn'  => substr($customer->msisdn, 3),
            'offerID' => $request->offer_id
        ]);
        $offer_data = json_decode($response_data['response']);
        $formatted_data = $this->prepareBuyOfferResponse($offer_data);

        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'You have successfully purchased offer');
    }
}
