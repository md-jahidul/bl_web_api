<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 12:12 PM
 */

namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Models\LmsPartnerOfferLike;
use App\Repositories\LmsPartnerOfferLikeRepository;
use App\Services\Banglalink\BanglalinkLoyaltyService;
use Illuminate\Http\JsonResponse;

class LoyaltyService extends ApiBaseService
{

    /**
     * @var BanglalinkLoyaltyService
     */
    protected $blLoyaltyService;
    /**
     * @var LmsPartnerOfferLikeRepository
     */
    private $likeRepository;

    /**
     * LoyaltyService constructor.
     * @param BanglalinkLoyaltyService $blLoyaltyService
     * @param LmsPartnerOfferLikeRepository $likeRepository
     */
    public function __construct(
        BanglalinkLoyaltyService $blLoyaltyService,
        LmsPartnerOfferLikeRepository $likeRepository
    )
    {
        $this->blLoyaltyService = $blLoyaltyService;
        $this->likeRepository = $likeRepository;
    }

    public function getPriyojonStatus($mobile)
    {
        $result = $this->blLoyaltyService->getPriyojonStatus($mobile);
        return $this->sendSuccessResponse($result['data'], 'Loyalty Status');
    }

    public function offerTest($segment)
    {
        $data = [];
        $offer_details['offer_id'] = $segment['offerID'];
        $data['offer_category_name'] = $segment['offerCategoryName'];
        $data['discount_rate'] = $segment['offerDescription'];
        $data['partner_logo'] = $segment['imageURL'];
        return $data;
    }

    private function parseRedeemOffer($catTitle, $catKey, $redeemOptions)
    {
        $offer_details = [];

        foreach ($redeemOptions as $key => $segment) {
            $catName = str_replace(' ', '_', strtolower($segment['offerCategoryName']));
            if ($catKey == $catName) {
                $offer_details['offer_category_name'] = $segment['offerCategoryName'];
                $offer_details['discount_rate'] = $segment['offerDescription'];
                $offer_details['partner_logo'] = $segment['imageURL'];
                $offer_details['partner_name'] = $segment['partnerName'];

//                switch ($catName) {
//                    case "physical_gift":
//                    case "internet_offers":
//                        $offerId = (int)$segment['offerID'];
//                        $likeInfo = $this->likeRepository->findOneByProperties(['offer_id' => $offerId]);
//                        $offer_details[] = [
//                            "offer_id" => $offerId,
//                            "offer_category_name" => $segment['offerCategoryName'],
//                            "discount_rate" => $segment['offerCategoryName'],
//                            "data" => $segment['offerDescriptionWeb'],
//                            "like" => $likeInfo['like'],
//                        ];
//                        $offer_details = $data;
//                        break;
//                }
            }
        }

        return $offer_details;
    }

    public function getRedeemOffers($mobile)
    {
        $redeemCats = [
            'internet_offers' => 'Internet Offers',
            'physical_gift' => 'Physical Gift',
            'bundles_offers' => 'Bundles Offers',
            'sms_offers' => 'Health and beauty care',
        ];

        $redeemOptions = $this->blLoyaltyService->getRedeemOptions($mobile);

        $catWithOffers = [];
        foreach ($redeemOptions['data'] as $catKey => $segment) {
//            $data = $this->parseRedeemOffer($item, $catKey, $redeemOptions['data']);

            $offer_details['offer_category_name'] = $segment['offerCategoryName'];
            $offer_details['discount_rate'] = $segment['offerDescription'];
            $offer_details['partner_logo'] = $segment['imageURL'];
            $offer_details['partner_name'] = $segment['partnerName'];


//            if ($data) {
//                $offer_details[] = $data;
//            }
        }

//        dd($catWithOffers);

        return $this->sendSuccessResponse($offer_details, 'Loyalty data');
    }

    private function parseOfferData($catTitle, $catKey, $redeemOptions)
    {
        $offer_details = [];

        foreach ($redeemOptions as $key => $segment) {
            $catName = str_replace(' ', '_', strtolower($segment['offerCategoryName']));
            if ($catKey == $catName) {
                switch ($catName) {
                    case "fashion_and_lifestyle":
                    case "electronics_and_furniture":
                    case "tours_and_travel":
                    case "health_and_beauty_care":
                    case "food_and_beverage":
                        $offerId = (int)$segment['offerID'];
                        $likeInfo = $this->likeRepository->findOneByProperties(['offer_id' => $offerId]);
                        $offer_details['offer_id'] = $offerId;
                        $offer_details['offer_category_name'] = $segment['offerCategoryName'];
                        $offer_details['discount_rate'] = $segment['offerDescription'];
                        $offer_details['partner_logo'] = $segment['imageURL'];
                        $offer_details['partner_name'] = $segment['partnerName'];
                        $offer_details['pop_up_details'] = $segment['offerLongDescription'];
                        $offer_details['like'] = $likeInfo ? $likeInfo['like'] : 0;
                        break;
                }
            }
        }
        return $offer_details;
    }

    public function partnerOffers($msisdn)
    {
        // This categories is fix
        $partnerCats = [
            'fashion_and_lifestyle' => 'Fashion and lifestyle',
            'electronics_and_furniture' => 'Electronics and furniture',
            'tours_and_travel' => 'Tours and travel',
            'health_and_beauty_care' => 'Health and beauty care',
            "food_and_beverage" => 'Food and beverage'
        ];
        // All Loyalty offers
        $redeemOptions = $this->blLoyaltyService->getRedeemOptions($msisdn);

        $catWithOffers = [];
        foreach ($partnerCats as $catKey => $item) {
            $data = $this->parseOfferData($item, $catKey, $redeemOptions['data']);
            if ($data) {
                $catWithOffers[] = $data;
            }
        }
        return $this->sendSuccessResponse($catWithOffers, 'Partner categories with offers');
    }

    /**
     * @param $offerId
     * @return JsonResponse|mixed
     */
    public function partnerOfferLike($offerId)
    {
        $likeInfo = $this->likeRepository->findOneByProperties(['offer_id' => $offerId]);
        if ($likeInfo) {
            $likeInfo->update([
                'like' => $likeInfo->like + 1
            ]);
        } else {
            $this->likeRepository->save([
                'offer_id' => $offerId,
                'like' => 1
            ]);
        }
        return $this->sendSuccessResponse([], 'Partner offers like successfully!');
    }

}
