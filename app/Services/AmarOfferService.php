<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Exceptions\AmarOfferBuyException;
use App\Exceptions\IdpAuthException;
use App\Exceptions\TokenInvalidException;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductRepository;
use App\Services\ApiBaseService;
use App\Services\CustomerService;
use App\Services\IdpIntegrationService;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmarOfferService extends BaseService
{
    /**
     * @var ApiBaseService
     */
    public $productRepository;
    public $productService;
    public $responseFormatter;
    protected const AMAR_OFFER_API_ENDPOINT = "/product-offer/offer/amar-offers";
    /**
     * @var CustomerService
     */
    protected $customerService;

    public function __construct
    (
        ApiBaseService $apiBaseService,
        CustomerService $customerService,
        ProductRepository $productRepository,
        ProductService $productService
    ) {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->responseFormatter = $apiBaseService;
        $this->customerService = $customerService;
    }

    public function getAmarOfferListUrl($msisdn)
    {
        return self::AMAR_OFFER_API_ENDPOINT . "?" . "msisdn=$msisdn";
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

    public function findByProductOfferId($allProducts, $availableProductIds)
    {
        $viewableProducts = [];
        foreach ($allProducts as $product) {
            if (in_array($product->productCore['offer_id'], $availableProductIds)) {
                array_push($viewableProducts, $product);
            }
        }
        return $viewableProducts;
    }

    private function parseOfferData($offer, $include_details = true)
    {
        $offer_details = [];
        $offer_description = $offer->offerDescriptionWeb;
        $offers = explode(';', $offer_description);
        $is_tariff_offer = false;

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
                    $offer_details ['minutes'] = (int)$data[1];
                    break;
                case "SMS":
                    $offer_details ['sms'] = (int)$data[1];
                    break;
                case "DATA":
                    if (strtolower($data[2]) == 'gb') {
                        $mb = (int)$data[1] * 1024 ;
                    } else {
                        $mb = (int)$data[1];
                    }
                    $offer_details ['internet'] = $mb;
                    break;
                case "TK":
                    $offer_details ['price'] = (int)$data[1];
                    break;
                case "VAL":
                    $offer_details ['validity'] = (int)$data[1];
                    $offer_details ['validity_unit'] = ucfirst(strtolower($data[2]));
                    break;
                case "CAT":
                    if ($data[1] == "DAT"){
                        $offerType = "data";
                    } elseif ($data[1] == "VOI"){
                        $offerType = "voice";
                    } else{
                        $offerType = $data[1];
                    }
                    $offer_details['offer_type'] = strtolower($offerType);
                    if ($data[1] == 'TAR') {
                        $is_tariff_offer = true;
                    };
                    break;
            }
        }

        if (!$is_tariff_offer) {
            $offer_details ['tag'] = null;
            return $offer_details;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function getAmarOfferList(Request $request)
    {
//        $amarOffers = $this->productRepository->amarOffers();

        $customerInfo = $this->customerService->getCustomerDetails($request);

        $response_data = $this->get($this->getAmarOfferListUrl(substr($customerInfo->msisdn, 3)));

        $formatted_data = $this->prepareAmarOfferList(json_decode($response_data['response']));


//        foreach (json_decode($response_data['response']) as $key=> $item){
//            $offerID[] = $item->offerID;
//        }
//        $amarOfferList = $this->findByProductOfferId($amarOffers, $offerID);
//
//        foreach ($amarOfferList as $product) {
//            $this->productService->bindDynamicValues($product, '', $product->productCore);
//            unset($product->productCore);
//        }

        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'Amar Offer List');
    }

    public function getAmarOfferDetails(Request $request)
    {
        $customer = $this->getCustomerInfo($request);

        $response_data = $this->get($this->getAmarOfferDetailsUrl($customer->msisdn, $request->offer_id));
        $offer_data = json_decode($response_data['response']);
        $formatted_data = $this->parseOfferData($offer_data, true);

        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'Amar Offer Details');
    }

//    private function prepareBuyOfferResponse($response)
//    {
//        if (isset($response->Status) && $response->Status == 'success') {
//            return [
//              'purchase_id' => $response->ID
//            ];
//        }
//
//        throw new AmarOfferBuyException();
//    }

//    public function buyAmarOffer(Request $request)
//    {
//        $customer = $this->getCustomerInfo($request);
//        $response_data = $this->post($this->getBuyAmarOfferUrl(), [
//            'msisdn'  => substr($customer->msisdn, 3),
//            'offerID' => $request->offer_id
//        ]);
//        $offer_data = json_decode($response_data['response']);
//        $formatted_data = $this->prepareBuyOfferResponse($offer_data);
//
//        return $this->responseFormatter->sendSuccessResponse($formatted_data, 'You have successfully purchased offer');
//    }
}
