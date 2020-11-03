<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 12:12 PM
 */

namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Services\Banglalink\BanglalinkLoyaltyService;

class LoyaltyService extends ApiBaseService
{

    /**
     * @var BanglalinkLoyaltyService
     */
    protected $blLoyaltyService;

    /**
     * LoyaltyService constructor.
     * @param BanglalinkLoyaltyService $blLoyaltyService
     */
    public function __construct(BanglalinkLoyaltyService $blLoyaltyService)
    {
        $this->blLoyaltyService = $blLoyaltyService;
    }

    public function getPriyojonStatus($mobile, $connectionType)
    {
        //  $result = $this->blLoyaltyService->getPriyojonStatus($mobile, $connectionType);
        // return $this->sendSuccessResponse($result['data'], 'Loyalty data');
        return $this->sendSuccessResponse([], 'Loyalty data');
    }

    public function getRedeemOptions($mobile)
    {
        $subscriberId = substr($mobile, 2);
        //$subscriberId = '1903303978'; //TODO: Remove from production

//        dd($subscriberId);

        $result = $this->blLoyaltyService->getRedeemOptions($mobile);
        return $this->sendSuccessResponse($result['data'], 'Loyalty data');
    }

    public function parseProduct($segment)
    {
        $product = [];
        $product['name'] = $segment['offerDescriptionWeb'];
        $product['offerShortDescription'] = $segment['offerShortDescription'];
        return $product;
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
                    $offer_details['offer_category_name'] = $segment['offerCategoryName'];
                    $offer_details['discount_rate'] = $segment['offerDescription'];
                    $offer_details['partner_logo'] = $segment['imageURL'];
                    $offer_details['partner_name'] = $segment['partnerName'];
                    $offer_details['pop_up_details'] = $segment['offerLongDescription'];

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
            'fashion_and_lifestyle' => 'Fashion_and_lifestyle',
            'electronics_and_furniture' => 'Electronics_and_furniture',
            'tours_and_travel' => 'Tours_and_travel',
            'health_and_beauty_care' => 'Health_and_beauty_care',
            "food_and_beverage" => 'Food_and_beverage'
        ];
        // All Loyalty offers
        $redeemOptions = $this->blLoyaltyService->getRedeemOptions($msisdn);

        $catWithOffers = [];
        foreach ($partnerCats as $catKey => $item) {
            $catWithOffers[] = $this->parseOfferData($item, $catKey, $redeemOptions['data']);
        }

        return $this->sendSuccessResponse($catWithOffers, 'Partner categories with offers');
    }

    public function redeemOffer($mobile)
    {

    }

}
