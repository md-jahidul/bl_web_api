<?php

namespace App\Services;

use App\Http\Resources\LoyaltyOfferCatResource;
use App\Http\Resources\OrangeClubTierResource;
use App\Http\Resources\PartnerOfferResource;
use App\Repositories\ComponentRepository;
use App\Repositories\LmsAboutBannerRepository;
use App\Repositories\LoyaltyTierRepository;
use App\Repositories\PartnerAreaRepository;
use App\Repositories\PartnerOfferCategoryRepository;
use App\Repositories\PartnerOfferRepository;
use App\Repositories\PriyojonRepository;
use App\Traits\CrudTrait;
use Illuminate\Database\QueryException;

class PartnerOfferService extends ApiBaseService {

    use CrudTrait;

    /**
     * @var $partnerOfferRepository
     */
    protected $partnerOfferRepository;

    /**
     * @var $priyojonRepository
     */
    protected $priyojonRepository;
    /**
     * @var LoyaltyTierRepository
     */
    private $loyaltyTierRepository;
    /**
     * @var PartnerOfferCategoryRepository
     */
    private $partnerOfferCategoryRepository;
    /**
     * @var ComponentRepository
     */
    private $componentRepository;
    /**
     * @var LmsAboutBannerRepository
     */
    private $lmsAboutBannerRepository;
    /**
     * @var PartnerAreaRepository
     */
    private $partnerAreaRepository;

    /**
     * PartnerOfferService constructor.
     * @param PartnerOfferRepository $partnerOfferRepository
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(
        PartnerOfferRepository $partnerOfferRepository,
        PriyojonRepository $priyojonRepository,
        ComponentRepository $componentRepository,
        lmsAboutBannerRepository $lmsAboutBannerRepository,
        LoyaltyTierRepository $loyaltyTierRepository,
        PartnerOfferCategoryRepository $partnerOfferCategoryRepository,
        PartnerAreaRepository $partnerAreaRepository
    ) {
        $this->partnerOfferRepository = $partnerOfferRepository;
        $this->priyojonRepository = $priyojonRepository;
        $this->componentRepository = $componentRepository;
        $this->lmsAboutBannerRepository = $lmsAboutBannerRepository;
        $this->loyaltyTierRepository = $loyaltyTierRepository;
        $this->partnerOfferCategoryRepository = $partnerOfferCategoryRepository;
        $this->partnerAreaRepository = $partnerAreaRepository;
        $this->setActionRepository($partnerOfferRepository);
    }

    /**
     * @param $obj
     * @param string $json_data
     * In PHP, By default objects are passed as reference copy to a new Object.
     */
    public function bindDynamicValues($obj, $json_data = 'other_attributes') {
        if (!empty($obj->{$json_data})) {
            foreach ($obj->{$json_data} as $key => $value) {
                $obj->{$key} = $value;
            }
        }
        unset($obj->{$json_data});
    }

    /**
     * @param $products
     * @return array
     */
    public function findRelatedProduct($products) {
        $data = [];
        foreach ($products as $product) {

            $findProduct = $this->findOne($product->related_product_id);
            array_push($data, $findProduct);
        }
        return $data;
    }

    /**
     * @Get_Priyojon_Offers form Partner table
     */
    public function priyojonOffers() {
        try {
            $partnerOffers = $this->partnerOfferRepository->offers();

            if ($partnerOffers) {
                $partnerOffers = PartnerOfferResource::collection($partnerOffers);
                return response()->success($partnerOffers, 'Data Found!');
            } else {
                return response()->error("Data Not Found!");
            }
        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }
    }

    /**
     * @Get_Priyojon_Offers form Partner table
     */
    public function discountOffers($page, $elg, $cat, $area, $searchStr) {
        try {

            $data['status'] = array(
                1 => "Silver",
                2 => "Gold",
                3 => "Platinum"
            );
            $data['categories'] = $this->partnerOfferRepository->getCategories();
            $data['areas'] = $this->partnerOfferRepository->getAreas();

            $offers = $this->partnerOfferRepository->discountOffers($page, $elg, $cat, $area, $searchStr);
            $data['offers'] = PartnerOfferResource::collection($offers);

            if ($data) {
//                $partnerOffers = PartnerOfferResource::collection($partnerOffers);
                return response()->success($data, 'Data Found!');
            }
            return response()->error("Data Not Found!");
        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }
    }

    /**
     * @Get_Priyojon_Offers form Partner table
     */
    public function offerLike($id) {
        try {

            $offer = $this->findOrFail($id);
            $offer->like = $offer->like + 1;

            if ($offer->save()) {
                $data['success'] = 1;
                $data['like'] = $offer->like;
                return $this->sendSuccessResponse($data, 'Offer Liked Successfully!');
            }
            $data['success'] = 0;
            return $this->sendErrorResponse('Process failed');
        } catch (QueryException $exception) {
            return response()->error("Something wrong", $exception);
        }
    }

    public function campaign()
    {
        $campaignOffers = $this->partnerOfferRepository->campaignOffers();
        $campaignOffers = array_map(function ($value) {
            $value->other_attributes = json_decode($value->other_attributes);
            return $value;
        }, $campaignOffers->toArray());
        return $this->sendSuccessResponse($campaignOffers, 'Partner Campaign Offers');
    }

    public function categoryOffers()
    {
        $offers = $this->partnerOfferCategoryRepository->loyaltyCatOffers();
        $data = LoyaltyOfferCatResource::collection($offers);
        return $this->sendSuccessResponse($data, 'Orange club Category offers');
    }

    public function tierOffers($showInHome = false)
    {
        $offers = $this->loyaltyTierRepository->offerByTier($showInHome);
        $data = OrangeClubTierResource::collection($offers);

        if ($showInHome) {
            return $data;
        }
        return $this->sendSuccessResponse($data, 'Orange club offers');
    }

    public function getComponentByPageType($pageType)
    {
        $data['component'] = $this->componentRepository->getComponentByPageType($pageType);
        $data['banner'] = $this->lmsAboutBannerRepository->findOneByProperties(['page_type' => "about_loyalty"], ['banner_image_url', 'banner_mobile_view', 'alt_text_en']);;
        return $this->sendSuccessResponse($data, 'About loyalty components');
    }

    public function getFilterOption()
    {
        $data = [
            'status'     => $this->loyaltyTierRepository->findByProperties(['status' => 1], ['title_en', 'title_bn', 'slug']),
            'categories' => $this->partnerOfferCategoryRepository->findByProperties(['status' => 1],
                [
                    'name_en',
                    'name_bn',
                    'page_header',
                    'page_header_bn',
                    'schema_markup',
                    'url_slug_en',
                    'url_slug_bn',
                ]),
            'area'       => $this->partnerAreaRepository->findByProperties([], ['area_en', 'area_bn']),
        ];
        return $this->sendSuccessResponse($data, 'All loyalty filter options');
    }
}
