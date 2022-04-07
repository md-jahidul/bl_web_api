<?php

namespace App\Services;


use App\Enums\OfferType;
use App\Exceptions\IdpAuthException;
use App\Models\Product;
use App\Models\ProductCore;
use App\Http\Resources\ProductCoreResource;
use App\Repositories\FourGLandingPageRepository;
use App\Repositories\ProductBookmarkRepository;
use App\Repositories\ProductRepository;
use App\Services\Banglalink\BalanceService;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\BanglalinkLoanService;
use App\Services\Banglalink\BanglalinkProductService;
use App\Traits\CrudTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ProductService extends ApiBaseService
{
    use CrudTrait;

    /**
     * @var $partnerOfferRepository
     */
    protected $productRepository;

    /**
     * @var BanglalinkProductService
     */
    protected $blProductService;

    /**
     * @var BanglalinkProductService
     */
    protected $blCustomerService;

    /**
     * @var BalanceService
     */
    protected $balanceService;

    /**
     * @var CustomerService
     */
    protected $customerService;

    /**
     * @var BanglalinkLoanService
     */
    protected $blLoanProductService;

    /***
     * @var ProductBookmarkRepository
     */
    protected $productBookmarkRepository;
    private $responseFormatter;

    protected const BALANCE_API_ENDPOINT = "/customer-information/customer-information";
    /**
     * @var FourGLandingPageRepository
     */
    private $fourGLandingPageRepository;

    /**
     * ProductService constructor.
     * @param ProductRepository $productRepository
     * @param BanglalinkProductService $blProductService
     * @param CustomerService $customerService
     * @param ProductBookmarkRepository $productBookmarkRepository
     * @param BanglalinkCustomerService $banglalinkCustomerService
     * @param BanglalinkLoanService $blLoanProductService
     * @param BalanceService $balanceService
     * @param FourGLandingPageRepository $fourGLandingPageRepository
     */
    public function __construct
    (
        ProductRepository $productRepository,
        BanglalinkProductService $blProductService,
        CustomerService $customerService,
        ProductBookmarkRepository $productBookmarkRepository,
        BanglalinkCustomerService $banglalinkCustomerService,
        BanglalinkLoanService $blLoanProductService,
        BalanceService $balanceService,
        FourGLandingPageRepository $fourGLandingPageRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->blProductService = $blProductService;
        $this->customerService = $customerService;
        $this->productBookmarkRepository = $productBookmarkRepository;
        $this->blLoanProductService = $blLoanProductService;
        $this->blCustomerService = $banglalinkCustomerService;
        $this->responseFormatter = new ApiBaseService();
        $this->balanceService = $balanceService;
        $this->fourGLandingPageRepository = $fourGLandingPageRepository;
        $this->setActionRepository($productRepository);
    }

    private function getPrepaidBalanceUrl($customer_id)
    {
        return self::BALANCE_API_ENDPOINT . '/' . $customer_id . '/prepaid-balances' . '?sortType=SERVICE_TYPE';
    }

    /***
     * @param $obj
     * @param string $json_data
     * @param null $data
     * @return mixed
     */
    public function bindDynamicValues($obj, $json_data = 'other_attributes', $data = null)
    {
        if (!empty($obj->{$json_data})) {
            foreach ($obj->{$json_data} as $key => $value) {
                $obj->{$key} = $value;
            }
            unset($obj->{$json_data});
        }
        // Product Core Data BindDynamicValues
        $data = json_decode($data);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $obj->{$key} = $value;
            }
            return $obj;
        }
    }

    /**
     * @param $products
     * @return array
     */
    public function findRelatedProduct($products = null, $otherProduct = null)
    {
        $data = [];
        foreach ($products as $product) {
            $findProduct = $this->productRepository->relatedProducts($product->related_product_id);
            if ($findProduct) {
                array_push($data, $findProduct);
            }
        }

        foreach ($data as $product) {
            $this->bindDynamicValues($product, '', $product->productCore);
            unset($product->productCore);
        }
        return $data;
    }

    /**
     * @param $products
     * @return array
     * @bindBondhoSimOffres
     */
    public function bindBondhoSimOffres($products)
    {
        $data = [];
        foreach ($products as $product) {
            $this->bindDynamicValues($product, 'offer_info');
            $this->bindDynamicValues($product, '', $product->productCore);
            array_push($data, $product);
            unset($product->productCore);
        }
        return $data;
    }

    public function trendingProduct()
    {
        $products = $this->productRepository->showTrendingProduct();
        foreach ($products as $product) {
            $this->bindDynamicValues($product, 'offer_info', $product->productCore);
            unset($product->productCore);
        }

        return $products;
    }

    /**
     * @param $request
     * @param $viewAbleProducts
     * @return array
     * @throws IdpAuthException
     */
    public function checkCustomerProduct($request, $viewAbleProducts)
    {
        $customer = $this->customerService->getCustomerDetails($request);
        $availableProducts = $this->getProductCodesByCustomerId($customer->customer_account_id);
        $viewAbleProducts = $this->filterProductsByUser($viewAbleProducts, $availableProducts);
        return $viewAbleProducts;
    }

    /**
     * @param $type
     * @param $offerType
     * @param $request
     * @return mixed
     */
    public function simTypeOffers($type, $offerType)
    {
        try {
            $products = $this->productRepository->simTypeProduct($type, $offerType);
            $viewAbleProducts = $products;

//            if ($this->isUserLoggedIn($request)) {
//                $viewAbleProducts = $this->checkCustomerProduct($request, $viewAbleProducts);
//            }

            if ($viewAbleProducts) {
                foreach ($viewAbleProducts as $product) {
                    $data = $product->productCore;
                    $this->bindDynamicValues($product, 'offer_info', $data);
                    unset($product->productCore);
                }
                return $this->sendSuccessResponse($viewAbleProducts, 'Data Found!');
//                return response()->success($viewAbleProducts, 'Data Found!');
            }
            return response()->error("Data Not Found!");
        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
    }

    public function simTypeOffersTypeWise($type, $offerType)
    {
        try {
            $products = ProductCore::whereHas('blProduct', function ($q) {
                $q->where('status', 1);
                $q->where('is_visible', 1);
                $q->where('offer_section_slug', '<>', 'gift');
                $q->where('offer_section_slug', '<>', 'transfer');
                $q->where('is_rate_cutter_offer', '<>', 1);
            })
            ->with('blProduct', 'detialTabs')->where('content_type', 'data')
            ->get()
            ->reject(function ($item) {
                return in_array($item->blProduct->scheduleStatus(), [3, 4]);
            });

            $item = [];
            $available_products = [];
            $pinnedProducts = [];

            // if (isset($request) && $request->bearerToken()) {
            //     $customer = $this->getCustomerInfo($request);
            //     $customer_id = $customer->customer_account_id;
            //     $available_products = $this->customerAvailableProductsService->getAvailableProductsByCustomer($customer_id, $request->header('platform'));

            //     $products = $this->checkMsisdnWiseProduct($products, $customer->phone);

            // } else {
            //     /**
            //      * Removing free (0 tk) product for guest users
            //      */
            //     $products = $products->reject(function ($item) {
            //         return !$item->mrp_price;
            //     });
            // }


            $products = $products->sortBy('mrp_price');
            $allPacks = [];
            $pinnedProducts['all'] = [];

            foreach ($products as $offer) {
                // filter if login user

                // if ($request->bearerToken() && (!in_array($offer->product_code, $available_products))) {
                //     continue;
                // }

                // if (is_null($offer->blProduct)) {
                //     continue;
                // }

                $pack = [
                    'product_code' => $offer->product_code,
                    'name' => $offer->commercial_name_en,
                    'commercial_name_en' =>  $offer->commercial_name_en ?? null,
                    'commercial_name_bn' =>  $offer->commercial_name_bn ?? null,
                    'tag' => $offer->blProduct->tag,
                    'tags' => optional($offer->blProduct->tags)->pluck('title'),
                    'tags_priority' => optional($offer->blProduct->tags)->min('priority') ?? 1000,
                    'price' => $offer->mrp_price,
                    'volume' => $offer->internet_volume_mb,
                    'validity' => $offer->validity,
                    'validity_unit' => ucfirst($offer->validity_unit),
                    'ussd_code' => $offer->activation_ussd,
                    'has_autorenew' => ($offer->renew_product_code) ? true : false,
                    'bonus' => [],
                    'points'                => (int)$offer->points,
                    'offer_breakdown_en'    => $offer->display_title_en,
                    'offer_breakdown_bn'    => $offer->display_title_bn,
                    'display_sd_vat_tax'    => $offer->display_sd_vat_tax,
                    'is_recharge' => ($offer->recharge_product_code) ? true : false,
                    'image' => ($offer->blProduct->media) ?
                        env('IMAGE_HOST') . '/storage/' . $offer->blProduct->media : null,
                    'data_main' => $offer->internet_volume_mb,
                    'data_bonus' => null,
                    'purchase_count' => (int) $offer->purchase_count,
                    'pin_to_top' => $offer->blProduct->pin_to_top
                ];

                $pattern = "/((^\d+(?:\.\d+)?)(GB|MB))[\s]*\(((\d+(?:\.\d+)?)(GB|MB))[\s]*\+[\s]?((\d+(?:\.\d+)?)(GB|MB)).*\)/";
                $name = $offer->commercial_name_en;
                
                if (preg_match($pattern, $name, $matches, PREG_OFFSET_CAPTURE)) {
                    $main_data = $matches[5][0];
                    $main_data_unit = strtolower($matches[6][0]);
                    if ($main_data_unit == 'gb') {
                        $main_data = $main_data * 1024;
                    }
                    $bonus_data = $matches[8][0];
                    $bonus_data_unit = mb_strtolower($matches[9][0]);

                    if ($bonus_data_unit == 'gb') {
                        $bonus_data = $bonus_data * 1024;
                    }

                    $pack ['data_main'] = $main_data;
                    $pack ['data_bonus'] = $bonus_data;
                }

                if ($pack['pin_to_top']) {
                    $pinnedProducts['all'][] = $pack;
                } else {
                    $allPacks[] = $pack;
                }
                $productTabs = $offer->detialTabs ?? [];

                foreach ($productTabs as $productTab) {
                    $item[$productTab->slug]['title'] = $productTab->name;
                    $item[$productTab->slug]['display_order'] = $productTab->sort;
                    if ($pack['pin_to_top']) {
                        $item[$productTab->slug]['pinned_products'][] = $pack;
                    } else {
                        $item [$productTab->slug]['packs'][] = $pack;
                    }
                }
            }

            // dd('item',$item);
            $sorted_data = collect($item)->sortBy('display_order');

            $data[] = [
                'type' => 'all',
                'title' => 'All',
                'packs' => array_values($pinnedProducts['all'] + $allPacks)
            ];

            foreach ($sorted_data as $category => $pack) {
                $data [] = [
                    'type' => $category,
                    'title' => $pack['title'],
                    'packs' => array_values(collect($pack['pinned_products'] ?? [])->merge($pack['packs'] ?? [])->toArray())
                ];
            }

            return $this->sendSuccessResponse($data, 'Internet packs list');

        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }        
    }

    /**
     * @param $mobile
     * @param $productCode
     * @return JsonResponse|mixed
     */
    public function eligible($mobile, $productCode)
    {
        $msisdn = "88" . $mobile;
        $customer = $this->blCustomerService->getCustomerInfoByNumber($msisdn);

        if ($customer->getData()->status_code == 500) {
            return $this->sendErrorResponse([], 'Customer not found', 404);
        }
        $customer_account_id = $customer->getData()->data->package->customerId;
        $availableProducts = $this->getProductCodesByCustomerId($customer_account_id);

        foreach ($availableProducts as $availableProduct){
            if ($availableProduct === strtoupper($productCode)) {
                $data = [
                    'is_eligible' => true
                ];
                return $this->sendSuccessResponse($data, 'This product eligible to you');
            }
        }
        return $this->sendSuccessResponse($data = ['is_eligible' => false], 'This product not eligible to you');
    }

    private function filterProductsByUser($allProducts, $availableProductIds)
    {

        $selectedProduct = [];
        foreach ($allProducts as $product) {
            if ($product->offer_category_id == OfferType::PACKAGES || $product->offer_category_id == OfferType::OTHERS){
                array_push($selectedProduct, $product->product_code);
            }
        }
        $margeArray = array_merge($selectedProduct, $availableProductIds);
        $viewableProducts = [];
        foreach ($allProducts as $product) {
            if (in_array($product->product_code, $margeArray)) {
                array_push($viewableProducts, $product);
            }
        }
        return $viewableProducts;
    }

    private function isUserLoggedIn($request)
    {
        if ($request->header('authorization')) {
            return true;
        }
        return false;
    }

    /**
     * @param $type
     * @param $id
     * @return mixed
     */
    public function details($slug)
    {
        try {
            $productDetail = $this->productRepository->detailProducts($slug);

            $rechargeCode = isset($productDetail->product_details->other_attributes['recharge_benefits_code']) ? $productDetail->product_details->other_attributes['recharge_benefits_code'] : null;
            $rechargeBenefitOffer = $this->productRepository->rechargeBenefitsOffer($rechargeCode);


            $specialProductOffers = isset($productDetail->product_details->other_attributes['special_product_id']) ? $productDetail->product_details->other_attributes['special_product_id'] : null;

            if ($specialProductOffers){
                foreach ($specialProductOffers as $productId)
                {
                    $specialProducts[] = $this->productRepository->relatedProducts($productId);
                }
            }

            $bondhoSImOffers = $this->productRepository->bondhoSimOffer();

            if ($productDetail) {
                $this->bindDynamicValues($productDetail, 'offer_info', $productDetail->productCore);
                $productDetail->other_related_products = $this->findRelatedProduct($productDetail->other_related_product);

                $productDetail->related_products = $this->findRelatedProduct($productDetail->related_product);

                if ($productDetail->other_offer_type_id == OfferType::BONDHO_SIM_OFFER){
                    if (!empty($bondhoSImOffers)){
                        $productDetail->other_related_products = $this->bindBondhoSimOffres($bondhoSImOffers);
                    }
                }

                if ($rechargeBenefitOffer){
                    $productDetail->recharge_benefit = $rechargeBenefitOffer;
                }

                if (isset($specialProducts)){
                    foreach ($specialProducts as $specialProduct) {
                        $product[] = $this->bindDynamicValues($specialProduct, 'offer_info', $specialProduct->productCore);
                        unset($specialProduct->productCore);
                    }
                    $productDetail->special_products = $product;
                }

                $this->bindDynamicValues($productDetail->related_products, 'offer_info');

                unset($productDetail->other_related_product);
                unset($productDetail->related_product);
                unset($productDetail->productCore);

                if( !empty($productDetail->product_details->banner_image_url) ){
                    $productDetail->product_details->banner_image_url = config('filesystems.image_host_url') . $productDetail->product_details->banner_image_url;
                    $productDetail->product_details->banner_image_mobile = config('filesystems.image_host_url') . $productDetail->product_details->banner_image_mobile;
                }

                return response()->success($productDetail, 'Data Found!');
            }

            return response()->error("Data Not Found!");

        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
    }

    public function getProductCodesByCustomerId($customerId)
    {
        $customerProducts = $this->blProductService->getCustomerProducts($customerId);

        $productIds = [];
        foreach ($customerProducts as $product) {
            array_push($productIds, $product['code']);
        }
        return $productIds;
    }

    /**
     * @param $request
     * @return JsonResponse
     * @throws IdpAuthException
     */
    public function customerProductBookmark($request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);
        $operationType = $request->operation_type;
        $productId = $request->product_id;

        if ($operationType == 'save') {
            $this->productBookmarkRepository->save([
                'mobile' => $customerInfo->phone,
                'product_id' => $productId,
                'module_type' => $request->module_type,
                'category' => $request->category,
            ]);
            return $this->sendSuccessResponse([], 'Bookmark saved successfully!');
        } else if ($operationType == 'delete') {
            $bookmarkProducts = $this->productBookmarkRepository->findByProperties(['mobile' => $customerInfo->phone]);
            foreach ($bookmarkProducts as $bookmarkProduct) {
                if ($bookmarkProduct->product_id == $productId) {
                    $bookmarkProduct->delete();
                    return $this->sendSuccessResponse([], 'Bookmark removed successfully!');
                }
            }
        }
        return $this->sendErrorResponse('Invalid operation');
    }

    /**
     * @param $request
     * @return mixed
     * @throws IdpAuthException
     */
    public function allRechargeOffers($request)
    {
        try {
            $rechargeOffers = $this->productRepository->rechargeOffers();

            // dd($rechargeOffers);

            if ($this->isUserLoggedIn($request)) {
                $rechargeOffers = $this->checkCustomerProduct($request, $rechargeOffers);
            }

            if ($rechargeOffers) {
                foreach ($rechargeOffers as $product) {
                    $data = $product->productCore;
                    $this->bindDynamicValues($product, 'offer_info', $data);
                    unset($product->productCore);
                }

                return response()->success($rechargeOffers, 'Data Found!');
            }
            return response()->error("Data Not Found!");

        } catch (QueryException $exception) {
            return response()->error("Something is Wrong!", $exception);
        }
    }

    public function rechargeOfferByAmount($amount)
    {
        $amount = (int)$amount;
        $rechargeOffer = $this->productRepository->rechargeOfferByAmount($amount);

        if( !empty($rechargeOffer) ){
            $rechargeOffer->price_tk = !empty($rechargeOffer->price_tk) ? (int)$rechargeOffer->price_tk : 0;
        }

        return $this->sendSuccessResponse($rechargeOffer, '');
    }

    /**
     * @param $request
     * @return mixed
     * @throws IdpAuthException
     */
    public function findCustomerSaveProducts($request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);
        $bookmarkProduct = $this->productBookmarkRepository->findByProperties(['mobile' => $customerInfo->phone]);
        if ($bookmarkProduct) {
            return response()->success($bookmarkProduct, 'Data Found!');
        }
        return response()->error("Data Not Found!");
    }


    /**
     * @param $request
     * @return mixed
     * @throws IdpAuthException
     */
    public function findCustomerProducts($request)
    {
        $customerInfo = $this->customerService->getCustomerDetails($request);
        $bookmarkProduct = $this->productBookmarkRepository->findByProperties(['mobile' => $customerInfo->phone]);

        $customerBookmarkProducts = [];
        foreach ($bookmarkProduct as $item)
        {
            $product = $this->productRepository->bookmarkProduct($item->product_code);
            if( !empty($product) ){
                array_push($customerBookmarkProducts, $product);
            }
        }
        foreach ($customerBookmarkProducts as $productCore) {
            $data = $productCore['productCore'];
            $this->bindDynamicValues($productCore, 'offer_info', $data);
            unset($productCore['productCore']);
        }
        if ($bookmarkProduct) {
            return response()->success($customerBookmarkProducts, 'Data Found!');
        }
        return response()->error("Data Not Found!");
    }

    /**
     * @param $productId
     * @return JsonResponse
     */
    public function like($productId)
    {
        try {
            $products = $this->productRepository->findOneByProperties(['product_code' => $productId]);
            if ($products) {
                $products['like'] = $products['like'] + 1;
                $products->update();
                return $this->sendSuccessResponse([], 'Product liked successfully!');
            }
        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
    }

    /**
     * @param $type
     * @return mixed
     */
    public function fourGInternet($type)
    {
        $fourGComponent = $this->fourGLandingPageRepository->getComponent('internet_offers');
        $internetOffers = $this->productRepository->fourGData($type);
        if ($internetOffers) {
            foreach ($internetOffers as $product) {
                $this->bindDynamicValues($product, 'offer_info', $product->productCore);
                unset($product->productCore);
            }
            $collection = [
                'component_title_en' => $fourGComponent->title_en,
                'component_title_bn' => $fourGComponent->title_bn,
                'current_page' => $internetOffers->currentPage(),
                'products' => $internetOffers->items(),
                'last_page' => $internetOffers->lastPage(),
                'per_page' => $internetOffers->perPage(),
                'total' => $internetOffers->total()
            ];
            $data = json_decode(json_encode($collection), true);
            return $this->sendSuccessResponse($data, '4G Internet Offers');
        }
    }
}
