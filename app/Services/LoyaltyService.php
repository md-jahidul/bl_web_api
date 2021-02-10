<?php
/**
 * Created by PhpStorm.
 * User: jahangir
 * Date: 12/9/19
 * Time: 12:12 PM
 */

namespace App\Services;


use App\Enums\HttpStatusCode;
use App\Exceptions\BLApiHubException;
use App\Models\LmsPartnerOfferLike;
use App\Repositories\LmsPartnerOfferLikeRepository;
use App\Repositories\PriyojonRepository;
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
     * @var $priyojonRepository
     */
    protected $priyojonRepository;

    /**
     * LoyaltyService constructor.
     * @param BanglalinkLoyaltyService $blLoyaltyService
     * @param LmsPartnerOfferLikeRepository $likeRepository
     * @param PriyojonRepository $priyojonRepository
     */
    public function __construct(
        BanglalinkLoyaltyService $blLoyaltyService,
        LmsPartnerOfferLikeRepository $likeRepository,
        PriyojonRepository $priyojonRepository
    )
    {
        $this->blLoyaltyService = $blLoyaltyService;
        $this->likeRepository = $likeRepository;
        $this->priyojonRepository = $priyojonRepository;
    }

    public function getPriyojonStatus($mobile)
    {
        $result = $this->blLoyaltyService->getPriyojonStatus($mobile);
        return $this->sendSuccessResponse($result['data'], 'Loyalty Status');
    }

    private function parseTelcoProduct($offer)
    {
        $offer_details = [];
        $offer_description = $offer['offerDescriptionWeb'];
        $offers = explode(';', $offer_description);

        $offer_details ['offer_id'] = $offer['offerID'];
        $offer_details['offer_category_name'] = $offer['offerCategoryName'];

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
                case "POINT":
                    $offer_details ['point'] = (int)$data[1];
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
                    if ($data[1] == "DAT") {
                        $offerType = "data";
                    } elseif ($data[1] == "VOI") {
                        $offerType = "voice";
                    } elseif ($data[1] == "MIX"){
                        $offerType = "bundles";
                    } else{
                        $offerType = $data[1];
                    }
                    $offer_details['offer_type'] = strtolower($offerType);
                    break;
            }
        }

        return $offer_details;
    }

    /**
     * @param $mobile
     * @return JsonResponse|mixed
     * @throws BLApiHubException
     */
    public function getRedeemOffers($mobile)
    {
        $redeemCats = [
            'internet_offers',
            'talk_time_offers',
//            'physical_gift'
        ];
        $redeemOptions = $this->blLoyaltyService->getRedeemOptions($mobile);
        $offer_details = [];
        foreach ($redeemOptions['data'] as $segment) {
            $catName = str_replace([' ', '-'], '_', strtolower($segment['offerCategoryName']));
            if (in_array($catName, $redeemCats)) {
                if ($catName == 'internet_offers' || $catName == 'talk_time_offers'){
                    $products = $this->parseTelcoProduct($segment);
                    $offer_details[] = $products;
                }else{
                    $offer_details[] = [
                        "offer_id" => $segment['offerID'],
                        "offer_category_name" => $segment['offerCategoryName'],
                        "offer_image" => $segment['imageURL'],
                        "offer_title" => $segment['offerDescription'],
                        "point" => $segment['offerPrice'],
                    ];
                }
            }
        }

        $priyojonMenu = $this->priyojonRepository->getMenuForSlug('redeem-point');

        $data = [
            'alias' => $priyojonMenu->alias,
            'url_slug_en' => $priyojonMenu->url_slug_en,
            'url_slug_bn' => $priyojonMenu->url_slug_bn,
            'offer_details' => $offer_details
        ];

        return $this->sendSuccessResponse($data, 'Loyalty data');
    }

    /**
     * @param $msisdn
     * @return JsonResponse|mixed
     * @throws BLApiHubException
     */
    public function partnerOffers($msisdn)
    {
        // This categories is fix
        $partnerCats = [
            'fashion_and_lifestyle',
            'electronics_and_furniture',
            'tours_and_travel' ,
            'health_and_beauty_care',
            'health_&_beauty_care',
            'food_and_beverage'
        ];
        // All Loyalty offers
        $redeemOptions = $this->blLoyaltyService->getRedeemOptions($msisdn);

        $catWithOffers = [];

        foreach ($redeemOptions['data'] as $segment) {
            $catName = str_replace([' ', '-'], '_', strtolower($segment['offerCategoryName']));
            if (in_array($catName, $partnerCats)) {
                $offerId = (int)$segment['offerID'];
                $likeInfo = $this->likeRepository->findOneByProperties(['offer_id' => $offerId]);
                $catWithOffers[] = [
                    'offer_id' => $offerId,
                    'offer_category_name' => $segment['offerCategoryName'],
                    'discount_rate' => $segment['offerShortDescription'],
                    'partner_logo' => $segment['imageURL'],
                    'partner_name' => $segment['partnerName'],
                    'pop_up_details' => $segment['offerLongDescription'],
                    'like' => $likeInfo ? $likeInfo['like'] : 0
                ];
            }
        }

        $priyojonMenu = $this->priyojonRepository->getMenuForSlug('partner');

        $data = [
            'alias' => $priyojonMenu->alias,
            'url_slug_en' => $priyojonMenu->url_slug_en,
            'url_slug_bn' => $priyojonMenu->url_slug_bn,
            'partnerOffers' => $catWithOffers
        ];

        return $this->sendSuccessResponse($data, 'Partner categories with offers');
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

    public function purchaseRedeemOffer($customer, $offerId)
    {
        $msisdn = substr($customer->phone, 1);
//        $msisdn = 1962424630;
        $response = $this->blLoyaltyService->redeemOfferPurchase($msisdn, $offerId);
        return $this->sendSuccessResponse($response, "Purchase request successfully send");
    }

}
