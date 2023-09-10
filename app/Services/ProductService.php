<?php

namespace App\Services;


use App\Enums\OfferType;
use App\Exceptions\IdpAuthException;
use App\Models\AlCoreProduct;
use App\Models\Product;
use App\Models\ProductCore;
use App\Http\Resources\ProductCoreResource;
use App\Models\MyBlProductTab;
use App\Repositories\ConfigRepository;
use App\Models\OfferCategory;
use App\Repositories\FourGLandingPageRepository;
use App\Repositories\OfferCategoryRepository;
use App\Repositories\ProductBookmarkRepository;
use App\Repositories\ProductDetailsSectionRepository;
use App\Repositories\ProductRepository;
use App\Services\Banglalink\AmarOfferService;
use App\Services\Banglalink\BalanceService;
use App\Services\Banglalink\BanglalinkCustomerService;
use App\Services\Banglalink\BanglalinkLoanService;
use App\Services\Banglalink\BanglalinkProductService;
use App\Services\Banglalink\CustomerAvailableProductsService;
use App\Traits\CrudTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

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

    /**
     * @var CustomerInfo
     */
    protected $customerInfo;

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
     * @var ConfigRepository
     */
    private $configRepository;
    /**
     * @var AmarOfferService
     */
    private $amarOfferService;
    /**
     * @var AlBannerService
     */
    private $alBannerService;
    /**
     * @var CustomerAvailableProductsService
     */
    private $customerAvailableProductsService;
    /**
     * @var ProductDetailsSectionRepository
     */
    private $productDetailsSectionRepository;
    /**
     * @var OfferCategoryRepository
     */
    private $offerCategoryRepository;

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
        FourGLandingPageRepository $fourGLandingPageRepository,
        ConfigRepository $configRepository,
        AmarOfferService $amarOfferService,
        AlBannerService $alBannerService,
        ProductDetailsSectionRepository $productDetailsSectionRepository,
        CustomerAvailableProductsService $customerAvailableProductsService,
        OfferCategoryRepository $offerCategoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->blProductService = $blProductService;
        $this->customerService = $customerService;
        $this->productBookmarkRepository = $productBookmarkRepository;
        $this->blLoanProductService = $blLoanProductService;
        $this->blCustomerService = $banglalinkCustomerService;
        $this->responseFormatter = new ApiBaseService();
        $this->balanceService = $balanceService;
        $this->fourGLandingPageRepository = $fourGLandingPageRepository;
        $this->configRepository = $configRepository;
        $this->amarOfferService = $amarOfferService;
        $this->alBannerService = $alBannerService;
        $this->offerCategoryRepository = $offerCategoryRepository;
        $this->productDetailsSectionRepository = $productDetailsSectionRepository;
        $this->customerAvailableProductsService = $customerAvailableProductsService;
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

    public function trendingProduct($params = [])
    {

        // $products = $this->productRepository->showTrendingProduct();

        // foreach ($products as $product) {
        //     $this->bindDynamicValues($product, 'offer_info', $product->productCore);
        //     unset($product->productCore);
        // }

        // return $products;

        /**
         * Shuvo-bs
         */

        $customerInfo = $params['customerInfo'] ?? null;
        $customerAvailableProducts = $params['customerAvailableProducts'] ?? [];
        $numberType  = ($customerInfo) ? $customerInfo->number_type : 'prepaid' ;

        $offerType = ['internet', 'voice', 'bundles'];
        $offerCategories =  OfferCategory::whereIn('alias', $offerType)->select('id', 'alias', 'name_en', 'name_bn')->get()?? [];
        $offerIDArr = collect($offerCategories)->pluck('id');

        try {
            $item = [];
            $data = [];
            $allPacks = [];
            // $products = $this->productRepository->simTypeProduct($type, $offerType);
            $products = $this->productRepository->offerProductsForYou($numberType, $offerIDArr, $customerAvailableProducts);


            if ($products) {
                foreach ($products as $product) {
                    $productData = $product->productCore;
                    $this->bindDynamicValues($product, 'offer_info', $productData);
                    unset($product->productCore);
                }
            }

            foreach ($products as $offer) {
                $pack = $offer->getAttributes();
                // foreach ($offerCategories as $category) {
                //     $item[$category->alias]['title_en'] = $category->name_en;
                //     $item[$category->alias]['title_bn'] = $category->name_bn;
                //     $item[$category->alias]['packs'][] = $pack;
                // }
                $item[$offer->offer_category->alias]['title_en'] = $offer->offer_category->name_en;
                $item[$offer->offer_category->alias]['title_bn'] = $offer->offer_category->name_bn;
                $item[$offer->offer_category->alias]['packs'][] = $pack;
            }

            $sortedData = collect($item);
            foreach ($sortedData as $category => $pack) {
                $data[] = [
                    'type' => $category,
                    'title_en' => $pack['title_en'],
                    'title_bn' => $pack['title_bn'],
                    'items' => array_values($pack['packs']) ?? []
                ];
            }

//            $allPacks = $products->map(function($item) { return $item->getAttributes(); });
            // if(!empty($data)) {
            //     array_unshift($data, [
            //         'type' => 'all',
            //         'title_en' => 'All',
            //         'title_bn' => Null,
            //         'packs' => $allPacks->toArray() ?? []
            //     ]);
            // }

            $amarOfferData = [];
            if (request()->header('authorization')) {
                $amarOffers = $this->amarOfferService->getAmarOfferListV2(request());
                if (!empty($amarOffers->getData()->data)) {
                    $amarOfferData[] = [
                        "type" => "amar-offer",
                        "title_en" => "Amar Offer",
                        "title_bn" => "আমার অফার",
                        "items" => $amarOffers->getData()->data
                    ];
                }
            }
            return array_merge($data, $amarOfferData);

        } catch (QueryException $exception) {
            return response()->error("Data Not Found!", $exception);
        }
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

    public function prepareAmarOffer()
    {
        //  $amarOffers = $this->amarOfferService->getAmarOfferListV2(request());
        $amarOffers = $this->amarOfferService->getAmarOfferListV2(request());

        if ($amarOffers->getData()->status_code == 200) {
            $offerCollection = collect($amarOffers->getData()->data)->groupBy('offer_type');
            $offersCat = [];
            $offersCat[] = [
                'type' =>  "all",
                'title_en' => "All",
                'title_bn' =>  "সকল",
                'packs'     => $amarOffers->getData()->data ?? [],
            ];
            if (!empty($offerCollection['data'])) {
                $offersCat[] = [
                    'type' =>  "internet",
                    'title_en' => "Internet",
                    'title_bn' =>  "ইন্টারনেট",
                    'packs'     => $offerCollection['data'],
                ];
            }

            if (!empty($offerCollection['voice'])) {
                $offersCat[] = [
                    'type' => "voice",
                    'title_en' => "Voice",
                    'title_bn' => "ভয়েস",
                    'packs' => $offerCollection['voice'],
                ];
            }

            if (!empty($offerCollection['sms'])) {
                $offersCat[] = [
                    'type' => "sms",
                    'title_en' => "SMS",
                    'title_bn' => "এস এম এস",
                    'packs' =>  $offerCollection['sms'],
                ];
            }

            return $offersCat;
        }

        return $this->responseFormatter->sendErrorResponse("Something went wrong!", "Internal Server Error", 500);
    }

    public function simTypeOffersTypeWise($type, $offerType)
    {
        try {
            $item = [];
            $data = [];
            $allPacks = [];

            if ($offerType == "amar-offer") {
                $data = $this->prepareAmarOffer();
                return $this->sendSuccessResponse($data, "{$offerType} packs list");
            }

            $products = $this->productRepository->simTypeProduct($type, $offerType);

            if ($products) {
                foreach ($products as $product) {
                    $productData = $product->productCore;
                    $tag = $product->tag;
                    $this->bindDynamicValues($product, 'offer_info', $productData);
                    $this->bindDynamicValues($product, 'offer_info', $tag);
                    unset($product->productCore);
                }
            }

            foreach ($products as $offer) {
                $pack = $offer->getAttributes();
                $productTabs = $offer->productCore->detialTabs()->where('my_bl_product_tabs.platform', MyBlProductTab::PLATFORM)->get() ?? [];

                foreach ($productTabs as $productTab) {
                    $item[$productTab->slug]['title_en'] = $productTab->name;
                    $item[$productTab->slug]['title_bn'] = $productTab->name_bn;
                    $item[$productTab->slug]['display_order'] = $productTab->sort;
                    $item[$productTab->slug]['packs'][] = $pack;
                }
            }
            $sortedData = collect($item)->sortBy('display_order');

            foreach ($sortedData as $category => $pack) {
                $data[] = [
                    'type' => $category,
                    'title_en' => $pack['title_en'],
                    'title_bn' => $pack['title_bn'],
                    'packs' => array_values($pack['packs']) ?? []
                ];
            }

            $allPacks = $products->map(function($item) { return $item->getAttributes(); });

            if(!empty($allPacks)) {
                array_unshift($data, [
                    'type' => 'all',
                    'title_en' => 'All',
                    'title_bn' => "সকল",
                    'packs' => $allPacks->toArray() ?? []
                ]);
            }

            $offerType = ucfirst($offerType);

            return $this->sendSuccessResponse($data, "{$offerType} packs list");

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

            $sections = $this->productDetailsSectionRepository->section($productDetail->id);
            foreach ($sections as $section){
                ($section->section_type == "tab_section") ? $isTab = true : $isTab = false;
            }

            $sectionData = [];
            foreach ($sections as $sectionKey => $section) {
                foreach ($section->components as $key => $component) {
                    if ($component->component_type == "bondho_sim_offer") {
                        $products = $this->productRepository->getProductById($component->other_attributes??[]);
                        $productData = [];
                        if (isset($products)){
                            foreach ($products as $product) {
                                $productData[] = array_merge($product->getAttributes(), $product->productCore->getAttributes());
                            }
                        }
                        $data['section'][$sectionKey]['components'][$key]['products'] = $productData;
                    }
                }

                $sectionData[] = $section;
            }

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


                $offerType = [
                    OfferType::INTERNET,
                    OfferType::VOICE,
                    OfferType::BUNDLES,
                    OfferType::CALL_RATE,
                    OfferType::RECHARGE_OFFER,
                    OfferType::PREPAID_PLANS,
                ];

                if (in_array($productDetail->offer_category_id, $offerType)) {
                    $banner = $this->alBannerService->getBanner($productDetail->id, 'product_details');
                } else {
                    $banner = $this->alBannerService->getBanner($productDetail->id, 'product_other_details');
                }

                unset($productDetail->offer_category);
                $productDetail->product_details->banner_image_url = $banner->image ?? null;
                $productDetail->product_details->banner_title_en = $banner->title_en ?? null;
                $productDetail->product_details->banner_title_bn = $banner->title_bn ?? null;
                $productDetail->product_details->banner_desc_en = $banner->desc_en ?? null;
                $productDetail->product_details->banner_desc_bn = $banner->desc_bn ?? null;

                $productDetail['section'] = $sectionData;
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
                $this->bindDynamicValues($product, 'offer_info', $product->tag);
                unset($product->productCore);
                unset($product->tag);
            }
            $collection = [
                'component_title_en' => $fourGComponent->title_en,
                'component_title_bn' => $fourGComponent->title_bn,
                'component_description_en' => $fourGComponent->description_en,
                'component_description_bn' => $fourGComponent->description_bn,
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

    public function getProductByCode($productId){

        return $this->productRepository->findOneByProperties(['product_code' => $productId]);
    }

    public function preSetRechargeAmount()
    {
        $defaultAmount = [20, 30, 50, 100, 150, 200];
        $data = $this->configRepository->findOneByProperties(['key' => 'recharge_pre_set_amount'], ['key', 'value']);

        if (isset($data)) {
            $data = array_map('intval', explode(',', $data->value));
        } else {
            $data = $defaultAmount;
        }
        return $this->sendSuccessResponse($data, "Recharge preset amount");
    }

    public function fallOfferProcess(AlCoreProduct $requestedProduct, $customerAvailableProducts, $balance): array
    {
        $products = AlCoreProduct::
        whereIn('product_code', $customerAvailableProducts)
        ->whereHas(
            'product',
            function ($q) {
                $q->where('status', 1)
                ->where('status', 1)
                ->where('special_product', 0)
                ->startEndDate();
            }
        )
        ->with('product')
        ->where('content_type', $requestedProduct->content_type)
        ->where('mrp_price', '<=', $balance)
        ->limit(5)
        ->orderBy('mrp_price', 'DESC')
        ->get();

        $fallbackProducts = [];
        foreach ($products as $product) {
            $fallbackProducts[] = [
                "name_en" => $product->commercial_name_en,
                "name_bn" => $product->commercial_name_bn,
                "rate_cutter_unit" => $product->rate_cutter_unit,
                "rate_cutter_offer" => $product->rate_cutter_offer,
                "price_tk" => $product->mrp_price,
                "validity_days" => $product->validity ?? 0,
                "validity_unit" => $product->validity_unit,
                "internet_volume_mb" => $product->internet_volume_mb,
                "sms_volume" => $product->sms_volume,
                "minute_volume" => $product->minute_volume,
                "callrate_offer" => $product->callrate_offer,
                "call_rate_unit" => $product->call_rate_unit,
                "sms_rate_offer" => $product->sms_rate_offer,
                "product_code" => $product->product_code,
                "renew_product_code" => $product->renew_product_code,
                "recharge_product_code" => $product->recharge_product_code,
                "offer_breakdown_en" => $product->product->product_details->offer_details_title_en ?? null,
                "offer_breakdown_bn" => $product->product->product_details->offer_details_title_bn ?? null
            ];
        }

        return $fallbackProducts;
    }

    public function fallbackOffers($request)
    {
        $data = [];
        $customer = $this->customerService->getAuthenticateCustomer($request);

        $product = AlCoreProduct::where('product_code', $request->product_code)->select('mrp_price', 'content_type')->first();
        $customerId = $customer->customer_account_id;

        $balance = $this->balanceService->getPrepaidBalance($customerId);
        $productPrice = $product->mrp_price;

        if ($productPrice > $balance) {
            $availableProducts = $this->customerAvailableProductsService->getAvailableProductsByCustomer($customerId);
            $data = $this->fallOfferProcess($product, $availableProducts, $balance);
        }
        return $this->sendSuccessResponse($data, 'Fall back offers');
    }

    public function validityUnitGenerator($validityUnit, $freeTest)
    {
        $freeTxtEn = null;
        $freeTxtBn = null;

        if ($validityUnit == "free_text") {
            $freeTxtEn = $freeTest['validity_free_text_en'] ?? null;
            $freeTxtBn = $freeTest['validity_free_text_bn'] ?? null;
        }

        $validityUnits = [
            'hour'  => ['en' => 'Hour', 'bn' => 'ঘন্টা'],
            'hours' => ['en' => 'Hours', 'bn' => 'ঘন্টা'],
            'day'   => ['en' => 'Day', 'bn' => 'দিন'],
            'days'  => ['en' => 'Days', 'bn' => 'দিন'],
            'month' => ['en' => 'Month', 'bn' => 'মাস'],
            'months' => ['en' => 'Months', 'bn' => 'মাস'],
            'year'  => ['en' => 'Year', 'bn' => 'বছর'],
            'years'  => ['en' => 'Years', 'bn' => 'বছর'],
            'free_text' => ['en' => $freeTxtEn, 'bn' => $freeTxtBn]
        ];

        return $validityUnits[$validityUnit];
    }

    public function trendingOffers()
    {
        $products = $this->productRepository->productOffers();
//        dd($products);
        foreach ($products as $product) {
            if ($product->sim_category_id == 1) {
                $prepaid[] = $this->productAttrPrepare($product);
            } else {
                $postpaid[] = $this->productAttrPrepare($product);
            }
        }
        $data = [
            [
                'title_en' => "Prepaid",
                'title_bn' => "প্রিপেইড",
                'offers' => $prepaid ?? []
            ],
            [
                'title_en' => "Postpaid",
                'title_bn' => "পোস্টপেইড",
                'offers' => $postpaid ?? []
            ]
        ];
        return $this->sendSuccessResponse($data, "Trending offers");
    }

    public function productAttrPrepare($product)
    {
        if ($product->sim_category_id == 1) {
            $urlEn = "/prepaid/" . $product->offer_category->url_slug . "/" . $product->url_slug;
            $urlBn = "/প্রিপেইড/" . $product->offer_category->url_slug_bn . "/". $product->url_slug_bn;
        } else {
            $urlEn = "/postpaid/" . $product->offer_category->url_slug . "/" . $product->url_slug;
            $urlBn = "/পোস্টপেইড/" . $product->offer_category->url_slug_bn . "/" . $product->url_slug_bn;
        }
        $dataValue = ($product->productCore->internet_volume_mb >= 1024);
        return [
            'product_code' => $product->product_code,
            'offer_type_en' => $product->offer_category->name_en,
            'offer_type_bn' => $product->offer_category->name_bn,
            'offer_alias' => $product->offer_category->alias,
            'title_en' => $product->name_en,
            'title_bn' => $product->name_bn,
            'data_volume' => $dataValue ? $product->productCore->internet_volume_mb / 1024 : $product->productCore->internet_volume_mb,
            'data_volume_unit_en' => $dataValue ? "GB" : "MB",
            'data_volume_unit_bn' => $dataValue ? "জিবি" : "এমবি",
            'minute_volume' => $product->productCore->minute_volume,
            'minute_volume_unit_en' => "Min",
            'minute_volume_unit_bn' => "মিনিট",
            'sms_volume' => $product->productCore->sms_volume,
            'sms_volume_unit_en' => "SMS",
            'sms_volume_unit_bn' => "এসএমএস",
            'call_rate_offer' => $product->productCore->callrate_offer,
            'call_rate_unit_en' => $product->productCore->call_rate_unit,
            'call_rate_unit_bn' => $product->productCore->call_rate_unit_bn,
            'price' => $product->productCore->price_tk,
            'validity' => $product->productCore->validity_days,
            'validity_unit_en' => isset($product->productCore->validity_unit) ? $this->validityUnitGenerator($product->productCore->validity_unit, $product->offer_info)['en'] : null,
            'validity_unit_bn' => isset($product->productCore->validity_unit) ? $this->validityUnitGenerator($product->productCore->validity_unit, $product->offer_info)['bn'] : null,
            'tag_name_en' => optional($product->tag)->tag_name_en,
            'tag_name_bn' => optional($product->tag)->tag_name_bn,
            'url_en' => $urlEn,
            'url_bn' => $urlBn,
        ];
    }

    public function eShopOffers($offerType)
    {
        if ($offerType == "four_g_offers") {
            $products = $this->productRepository->fourGData('', true);
        } else {
            $offerCat =  $this->offerCategoryRepository->findOneByProperties(['alias' => $offerType], [
                'id', 'url_slug', 'url_slug_bn', 'alias'
            ]);
            $products = $this->productRepository->productOffers($offerCat->id);
        }

        foreach ($products as $product) {
            if ($product->sim_category_id == 1) {
                $prepaid[] = $this->productAttrPrepare($product);
            } else {
                $postpaid[] = $this->productAttrPrepare($product);
            }
        }

        $data = [
            [
                'title_en' => "Prepaid",
                'title_bn' => "প্রিপেইড",
                'offers' => $prepaid ?? []
            ],
            [
                'title_en' => "Postpaid",
                'title_bn' => "পোস্টপেইড",
                'offers' => $postpaid ?? []
            ]
        ];
        return $this->sendSuccessResponse($data, "New SIM offers");
    }
}
