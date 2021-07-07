<?php

namespace App\Services;

use App\Http\Resources\PartnerOfferResource;
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
     * PartnerOfferService constructor.
     * @param PartnerOfferRepository $partnerOfferRepository
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(
        PartnerOfferRepository $partnerOfferRepository,
        PriyojonRepository $priyojonRepository
    ) {
        $this->partnerOfferRepository = $partnerOfferRepository;
        $this->priyojonRepository = $priyojonRepository;
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

}
