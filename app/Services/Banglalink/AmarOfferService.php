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

//        $offers ['internet'] = [
//            "type" => $data['offer_type'],
//            "title_en" => $data['offer_type'],
//            "title_bn" => $data['offer_type'],
//            "packs" => [$data]
//        ];

        $demoData = [
            [
              "offer_id" => "21466",
              "offer_name" => "1GB-3 Days - 30TK",
              "description" => "1GB(900MB+124MB Bonus) for 3 Days at 30TK (22.51 + 7.49 All Taxes)",
              "minute_volume" => 0,
              "sms_volume" => 0,
              "internet_volume_mb" => 1024.0,
              "price_tk" => 30,
              "validity_days" => 3,
              "validity_unit" => "Days",
              "offer_type" => "data"
            ],
            [
                "offer_id" => "21558",
              "offer_name" => "2.5GB-3 Days - 50TK",
              "description" => "2.5GB(1.75GB+0.75GB Bonus) for 3 Days at 50TK (37.52 + 12.48 All Taxes)",
              "minute_volume" => 0,
              "sms_volume" => 0,
              "internet_volume_mb" => 2560.0,
              "price_tk" => 50,
              "validity_days" => 3,
              "validity_unit" => "Days",
              "offer_type" => "data"
            ],
            [
                "offer_id" => "21452",
              "offer_name" => "3.5GB-3 Days - 55TK",
              "description" => "3.5GB(3GB+0.5GB Bonus) for 3 Days at 55TK (41.28 + 13.72 All Taxes)",
              "minute_volume" => 0,
              "sms_volume" => 0,
              "internet_volume_mb" => 3584.0,
              "price_tk" => 55,
              "validity_days" => 3,
              "validity_unit" => "Days",
              "offer_type" => "data"
            ],
            [
                "offer_id" => "20280",
              "offer_name" => "60min-7 Days - 37TK",
              "description" => "60Min for 7 Days at 37TK (27.77 + 9.23 All Taxes)",
              "minute_volume" => 60,
              "sms_volume" => 0,
              "internet_volume_mb" => 0.0,
              "price_tk" => 37,
              "validity_days" => 7,
              "validity_unit" => "Days",
              "offer_type" => "voice"
            ],
            [
                "offer_id" => "20547",
              "offer_name" => "90min-7 Days - 57TK",
              "description" => "85Min for 7 Days at 57TK (42.78 + 14.22 All Taxes)",
              "minute_volume" => 85,
              "sms_volume" => 0,
              "internet_volume_mb" => 0.0,
              "price_tk" => 57,
              "validity_days" => 7,
              "validity_unit" => "Day",
              "offer_type" => "voice"
            ],
            [
                "offer_id" => "21246",
              "offer_name" => "10GB-3 Days - 89TK",
              "description" => "10GB(9GB+1GB Bonus) for 3 Days at 89TK  (66.79 + 22.21 All Taxes)",
              "minute_volume" => 0,
              "sms_volume" => 0,
              "internet_volume_mb" => 10240.0,
              "price_tk" => 89,
              "validity_days" => 3,
              "validity_unit" => "Days",
              "offer_type" => "data"
            ]
        ];

        $collection = collect($demoData)->groupBy('offer_type');

        $offersCat = [
            [
                'type' =>  "data",
                'title_en' => "Internet",
                'title_bn' =>  "Internet BN",
                'pack'     => isset($collection['data']) ? $collection['data'] : [],
            ],
            [
                'type' =>  "voice",
                'title_en' => "Voice",
                'title_bn' =>  "Voice",
                'pack'     => isset($collection['voice']) ? $collection['voice'] : [],
            ],
            [
                'type' =>  "SMS",
                'title_en' => "SMS",
                'title_bn' =>  "SMS BN",
                'pack'     => isset($collection['sms']) ? $collection['sms'] : []
            ],
            [
                'type' =>  "all",
                'title_en' => "All",
                'title_bn' =>  null,
                'pack'     => []
            ]
        ];
        dd($offersCat);


        $offers = [];
        foreach ($demoData as $offer) {
            if ($offer['offer_type'] == "data") {
                $offers['type'] = $offer['offer_type'];
                $offers['packs'][] = $offer;
            } elseif ($offer['offer_type'] == "voice") {
                $offers['type'] = $offer['offer_type'];
                $offers['packs'][] = $offer;
            }

//            if ($data = $this->parseOfferData($offer)) {
//                $offers [] = $data;
//                dd($data);
//                $offers = [
//                    "type" => "Internet",
//                    "title_en" => "Internet",
//                    "title_bn" => "Internet",
//                ];
//            }
        }
        dd($offers);
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
//            dd($data);
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
//        dd($offer_details);
        return $offer_details;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAmarOfferList(Request $request)
    {
        $formatted_data = $this->prepareAmarOfferList('');
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
            return $formatted_data;
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
            'channel' => 'Website',
            'channelId' => 7,
            'msisdn'  => substr($customer->msisdn, 3),
            'offerID' => $request->offer_id
        ]);


//        dd($response_data);
        $offer_data = json_decode($response_data['response']);
        $formatted_data = $this->prepareBuyOfferResponse($offer_data);

        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'You have successfully purchased offer');
    }
}
