<?php

namespace App\Services\Banglalink;

use App\Enums\HttpStatusCode;
use App\Exceptions\AmarOfferBuyException;
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

    public function getAmarOfferList(Request $request)
    {
        $amarOffers = $this->productRepository->amarOffers();

        $customerInfo = $this->customerService->getCustomerDetails($request);

        $response_data = $this->get($this->getAmarOfferListUrl(substr($customerInfo->msisdn, 3)));

        foreach (json_decode($response_data['response']) as $key=> $item){
            $offerID[] = $item->offerID;
        }

        $amarOfferList = $this->findByProductOfferId($amarOffers, $offerID);

        foreach ($amarOfferList as $product) {
            $this->productService->bindDynamicValues($product, '', $product->productCore);
            unset($product->productCore);
        }

        return $this->responseFormatter->sendSuccessResponse($amarOfferList, 'Amar Offer List');
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
